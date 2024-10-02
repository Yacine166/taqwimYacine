<?php

namespace App\Models;

use Carbon\Carbon;
use App\Helpers\Helper;
use App\Models\Exception;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
  use HasFactory;
  protected  $guarded = [];
  /**
   * Get all of the organizations for the Event
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
   */
  public function organizations(): BelongsToMany
  {
    return $this->BelongsToMany(Organization::class)->withPivot('id','deadline', 'completed_at', 'notification_date')
      ->withTimestamps()->using('App\Models\EventOrganization');
  }

  /**
   * Get the parameter that owns the Event
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
   */
  public function parameters(): BelongsToMany
  {
    return $this->BelongsToMany(Parameter::class)->withPivot(['value', 'condition', 'parameter_id'])->withTimestamps();
  }

  /**
   * Get the type that owns the Event
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function category(): BelongsTo
  {
    return $this->belongsTo(Category::class);
  }

  /**
   * Get the exceptions associated with the Event
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function exceptions(): HasMany
  {
      return $this->hasMany(Exception::class);
  }

  public function attachOrganizations($organizations = null, $detach_unqualified_organizations = false)
  {
    if (is_null($organizations)) $organizations = Organization::all()->load('parameters');

    if ($organizations->isEmpty()) throw new \Exception("No Organizations are available to attach");

    $ids = $this->filterOrganizationsIds($organizations);

    if ($detach_unqualified_organizations) $this->detachUnqualified($ids->all, $ids->qualified);

    if ($ids->newQualified == []) return false;

    $org_event_deadline_notification = $this->generateEventsInstances($ids->newQualified, $organizations);

    if ($org_event_deadline_notification->values == []) return false;

    return  batch()->insert(new EventOrganization, $org_event_deadline_notification->columns, $org_event_deadline_notification->values);
  }


  /**
   * filter and return organizations ids
   * @return stdClass
   */
  public function filterOrganizationsIds(Collection $organizations)
  {
    $organizations_ids = $organizations->pluck('id')->toArray();

    $qualified_organizations_ids = $this->organizationsMeetConditions($organizations);

    $event_organizations_ids = $this->organizations()->wherePivot('deadline', '>=', now()->startOfYear())->pluck('organizations.id')->unique()->toArray();

    $new_qualified_organizations_ids =  array_diff($qualified_organizations_ids, $event_organizations_ids);

    return (object) [
      'all' => $organizations_ids,
      'qualified' => $qualified_organizations_ids,
      'newQualified' => $new_qualified_organizations_ids,
    ];
  }

  public function detachUnqualified(array $all_organizations_ids, array $qualified_organizations_ids): bool
  {
    $detach_ids = array_diff($all_organizations_ids, $qualified_organizations_ids);
    return $detach_ids ?
      DB::table('event_organization')
      ->whereIn('organization_id', $detach_ids)
      ->where('event_id', $this->id)
      ->where('deadline', '>=', now()->startOfYear())
      ->delete()
      : false;
  }


  /**
   * get the IDs of the organizations which meet the conditions.
   * @return array
   */
  public function organizationsMeetConditions($organizations)
  {
    $event = $this->load('parameters');
    if (!$event->parameters->isEmpty()) {
      return $organizations->map(function ($organization) use ($event) {
        if ($event->matchOrganization($organization)) return $organization->id;
      })->filter()->all();
    } else {
      return $organizations->pluck('id')->toArray();
    }
  }


  /**
   * create deadlines & notif dates for organizations.
   * @param array $events_ids  the ids of the events
   * @return stdClass
   */
  public function  generateEventsInstances($organizations_id, $organizations)
  {
    $org_event_deadline_notification = [];
    $category_organization = DB::table('category_organization')->where('category_id', $this->category->id)->get();
    if ($category_organization->isEmpty()) throw new \Exception("No Notifications settings are set for this category, check category_event table");

    $deadlines = $this->cycle_month == "monthly" ?
      Helper::startingDate(now()->startOfMonth(), $this->cycle, $this->cycle_day)->monthsUntil(now()->addYearNoOverflow()->endOfYear(), $this->cycle)

      : (Carbon::createFromFormat('Y-m-d', now()->year . "-$this->cycle_month-$this->cycle_day")> now()->startOfMonth() ?
        [Carbon::createFromFormat('Y-m-d', now()->year . "-$this->cycle_month-$this->cycle_day"), Carbon::createFromFormat('Y-m-d', now()->addYear()->year . "-$this->cycle_month-$this->cycle_day")]
        :[Carbon::createFromFormat('Y-m-d', now()->addYear()->year . "-$this->cycle_month-$this->cycle_day")]);

    if ($this->exceptions()->exists()) {
      $exceptions = $this->exceptions;
      $deadlines = collect($deadlines)->map(function ($deadline) use ($exceptions) {
        $exception = $exceptions->where("old_month", $deadline->month)->where("old_day", $deadline->day)->first();
        return  $exception ? $deadline->month($exception->new_month)->day($exception->new_day) : $deadline;
      })->all();
    }

    foreach ($organizations_id as $id) {
      $organization = $organizations->find($id);

      foreach ($deadlines as $deadline) {
        $notification_date = (clone $deadline)->subDays(
          $category_organization->where('organization_id', $organization->id)
            ->first()->days_before_notification
        );
        $org_event_deadline_notification[] = [$id, $this->id, $deadline, $notification_date];
      }
    }

    return (object)[
      'columns' => ['organization_id', 'event_id', 'deadline', 'notification_date'],
      'values' => $org_event_deadline_notification
    ];
  }



  /**
   * check if event matches organization
   * 
   */
  public function matchOrganization($organization)
  {

    $event_parameters = $this->parameters;
    $match = false;
    $organization_parameters = $organization->parameters;
    $event_parameters->each(function ($event_parameter)
    use ($organization, $organization_parameters, $event_parameters, &$match) {

      $options = $event_parameter->options;
      $organization_option = $organization_parameters->find($event_parameter->id)?->pivot->option;
      if (!$organization_option)
        throw new \Exception("parameter '$event_parameter->name' id=$event_parameter->id  not found for Organization id=$organization->id");

      $event_option = $event_parameters->where('id', $event_parameter->id)->first()->pivot->value;
      $condition = $event_parameters->where('id', $event_parameter->id)->first()->pivot->condition;
      $organization_index = array_search($organization_option, $options);
      $event_index = array_search($event_option, $options);

      $match = Helper::bool($organization_index, $event_index, $condition);

      //break the loop
      if (!$match)  return false;
    });
    return $match;
  }
}
