<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Event;
use App\Helpers\Helper;
use App\Models\EventOrganization;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use stdClass;

class Organization extends Model
{
  use HasFactory;
  protected $guarded = [];

  /**
   * Get the user that owns the Organization
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  /**
   * Get all of the events for the Organization
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
   */
  public function events(): BelongsToMany
  {
    return $this->belongsToMany(Event::class)
      ->withPivot('id', 'completed_at', 'notification_date', 'deadline')
      ->withTimestamps()->using('App\Models\EventOrganization');
  }

  /**
   * Get all of the parameters for the Organization
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
   */
  public function parameters(): BelongsToMany
  {
    return $this->belongsToMany(Parameter::class)->withPivot('option')->withTimestamps();
  }

  /**
   * The categories that belong to the Organization
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
   */
  public function categories(): BelongsToMany
  {
    return $this->belongsToMany(Category::class)->withPivot(['is_notifable', 'days_before_notification'])->withTimestamps();
  }

  /**
   * custom upsert & detach events for the organization
   * @method attachEvents()  @return array|bool
   * @param \Eloquent\Collection|null $events if null given all the events are passed
   * @param bool $detach_unqualified_events clear this year's history of unqualified events
   */
  public function attachEvents(Collection $events = null, bool $detach_unqualified_events = false)
  {
    if (is_null($events)) $events = Event::all()->load('parameters');

    if ($events->isEmpty()) throw new \Exception("No Events are available to attach");

    $ids = $this->filterEventsIds($events);

    if ($detach_unqualified_events) $this->detachUnqualified($ids->all, $ids->qualified);

    if ($ids->newQualified == []) return false;

    $org_event_deadline_notification = $this->generateEventsInstances($ids->newQualified, $events);

    if ($org_event_deadline_notification->values == []) return false;

    return  batch()->insert(new EventOrganization, $org_event_deadline_notification->columns, $org_event_deadline_notification->values);
  }
  /**
   * filter and return events ids
   * @return stdClass
   */
  public function filterEventsIds(Collection $events)
  {
    $events_ids = $events->pluck('id')->toArray();
    $events_without_conditions_ids = $events->where('parameters', Collection::empty())->pluck('id');
    $events_meets_conditions_ids = $this->eventsMeetsConditionsIds($events);
    $qualified_events_ids = [...$events_without_conditions_ids, ...$events_meets_conditions_ids];
    $organization_events_ids = $this->events()->wherePivot('deadline', '>=', now()->startOfYear())->pluck('events.id')->unique()->toArray();
    $new_qualified_events_ids =  array_diff($qualified_events_ids, $organization_events_ids);

    return (object) [
      'all' => $events_ids,
      'withoutCondition' => $events_without_conditions_ids,
      'meetsConditions' => $events_meets_conditions_ids,
      'qualified' => $qualified_events_ids,
      'newQualified' => $new_qualified_events_ids
    ];
  }
  /**
   * detach this year's unqualified events form organization's events
   * @return bool
   */
  public function detachUnqualified(array $all_events_ids, array $qualified_events_ids): bool
  {
    $detach_ids = array_diff($all_events_ids, $qualified_events_ids);
    return $detach_ids ?
      DB::table('event_organization')
      ->whereIn('event_id', $detach_ids)
      ->where('organization_id', $this->id)
      ->where('deadline', '>=', now()->startOfYear())
      ->delete()
      : false;
  }

  /**
   * get the IDs of the events which meet the conditions.
   * @return array
   */
  public function eventsMeetsConditionsIds($events)
  {
    $eventsHaveParameters = $events->where('parameters', '!=', Collection::empty());
    $organization = $this->load('parameters');
    return $eventsHaveParameters->map(function ($event) use ($organization) {
      if ($event->matchOrganization($organization)) return $event->id;
    })->filter()->all();
  }

  /**
   * create deadlines & notif dates for events.
   * @param array $events_ids  the ids of the events
   * @return stdClass
   */
  public function  generateEventsInstances($events_ids, $events)
  {
    $events->load('exceptions');
    $org_event_deadline_notification = [];
    $category_organization = DB::table('category_organization')->where('organization_id', $this->id)->get();
    if ($category_organization->isEmpty()) throw new \Exception("No Notifications settings are set for this organization, check category_event table");
    foreach ($events_ids as $id) {
      $event = $events->find($id);

      $deadlines = $event->cycle_month == "monthly" ?
        Helper::startingDate(now()->startOfMonth(), $event->cycle, $event->cycle_day)->monthsUntil(now()->addYearNoOverflow()->endOfYear(), $event->cycle)

        : (Carbon::createFromFormat('Y-m-d', now()->year . "-$event->cycle_month-$event->cycle_day") > now()->startOfMonth() ?
          [Carbon::createFromFormat('Y-m-d', now()->year . "-$event->cycle_month-$event->cycle_day"), Carbon::createFromFormat('Y-m-d', now()->addYear()->year . "-$event->cycle_month-$event->cycle_day")]
          :[Carbon::createFromFormat('Y-m-d', now()->addYear()->year . "-$event->cycle_month-$event->cycle_day")]);

      if ($event->exceptions()->exists()) {

        $exceptions = $event->exceptions;
        $deadlines = collect($deadlines)->map(function ($deadline) use ($exceptions) {

          $exception = $exceptions->where("old_month", $deadline->month)->where("old_day", $deadline->day)->first();

          return  $exception ? $deadline->month($exception->new_month)->day($exception->new_day) : $deadline;
        })->all();
      }

      foreach ($deadlines as $deadline) {
        $notification_date = (clone $deadline)->subDays(
          $category_organization->where('category_id', $event->category_id)
            ->first()->days_before_notification
        );

        $org_event_deadline_notification[] = [$this->id, $id, $deadline, $notification_date];
      }
    }

    return (object)[
      'columns' => ['organization_id', 'event_id', 'deadline', 'notification_date'],
      'values' => $org_event_deadline_notification
    ];
  }
}
