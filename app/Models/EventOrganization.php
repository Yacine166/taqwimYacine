<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\HasOne;
class EventOrganization extends Pivot
{
  protected $casts=['completed_at'=>'datetime','deadline'=> 'date','notification_date'=>'date'];
  /**
   * Get the event associated with the EventOrganization
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasOne
   */
  public function event(): HasOne
  {
    return $this->hasOne(Event::class, 'id', 'event_id');
  }

  /**
   * Get the organization associated with the OrganizationOrganization
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasOne
   */
  public function organization(): HasOne
  {
    return $this->hasOne(Organization::class, 'id', 'organization_id')->with('user');
  }


  /**
   * update the notification dates based on the days_before_notification and the deadline in event_organization
   * 
   * uses mass update with one query usig Mavinoo\Batch\Batch laravel-batch package
   * 
   * Alert: runs the query even if the values are the same
   * @param Illuminate\Database\Eloquent\Collection $org
   * @param Category $cat
   * @return int|bool
   */
  public static function updateNotificationDates( $org, Category $category)
  {
    $updates = [];
    $events = $org->events()->where('category_id', $category->id)->get();

    if (!boolval($events->toArray())) {
      return true;
    }

    foreach ($events as $event) {
      $notification_date =$event->pivot->deadline->subDays($org->categories->find($category->id)->pivot->days_before_notification);

      array_push($updates, [
        'id' => $event->pivot->id,
        'notification_date' => $notification_date,
      ]);
    }

    $table_model = new EventOrganization;
    return  batch()->update($table_model, $updates, 'id');
  }


  public static function updateNotificationDatesFromEvents($events)
  {
    $updates = [];

    if (!boolval($events->toArray())) {
      return true;
    }

    foreach ($events as $event) {
      $notification_date = Carbon::createFromFormat('Y-m-d', $event->deadline)->subDays($event->days_before_notification);

      array_push($updates, [
        'id' => $event->id,
        'notification_date' => $notification_date
      ]);
    }
    $table_model = new EventOrganization;
    return  batch()->update($table_model, $updates, 'id');
  }
}
