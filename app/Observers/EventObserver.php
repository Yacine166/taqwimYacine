<?php

namespace App\Observers;

use stdClass;
use App\Models\Event;
use App\Models\Exception;
use Illuminate\Support\Facades\DB;

class EventObserver
{


  /**
   * Handle the Event "updated" event.
   */
  public function updated(Event $event): void
  {
    if ($event->isDirty('cycle_day') || $event->isDirty('cycle_month')) {
      $exceptions = Exception::where('event_id', $event->id)->get();
      $exceptions->each(function ($exception) {
        $exception->disable();
      });
      Exception::destroy($exceptions);
      $deadline = new stdClass;
      $deadline->day = strlen($event->cycle_day) == 1 ?  sprintf('%02d', $event->cycle_day) : $event->cycle_day;
      $deadline->month = strlen($event->cycle_month) == 1 ?  sprintf('%02d', $event->cycle_month) : $event->cycle_month;

      $notification_date = new stdClass;
      $notification_date->dayDiff = $event->cycle_day - $event->getOriginal('cycle_day');
      $notification_date->monthDiff =  $deadline->month == 'monthly' ? 0
        : $event->cycle_month - $event->getOriginal('cycle_month');

      $deadline->month == 'monthly' ?
        $query = (object) [
          "deadline" => "DATE(CONCAT(YEAR(deadline),MONTH(deadline),'$deadline->day'))",
          "notification_date" => "DATE_ADD(notification_date,INTERVAL $notification_date->dayDiff DAY)"
        ] :
        $query = (object) [
          "deadline" => "DATE(CONCAT(YEAR(deadline),'$deadline->month','$deadline->day'))",
          "notification_date" => " DATE_ADD(DATE_ADD(notification_date,INTERVAL $notification_date->monthDiff MONTH),INTERVAL $notification_date->dayDiff DAY)"
        ];

      DB::table('event_organization')
        ->where('event_id', $event->id)
        ->where('deadline', '>=', now()->startOfYear())
        ->update([
          'deadline' => DB::raw($query->deadline),
          'notification_date' => DB::raw($query->notification_date)
        ]);
    }
  }
}
