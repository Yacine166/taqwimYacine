<?php

namespace App\Models;

use stdClass;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Exception extends Model
{
  use HasFactory;
  protected $guarded = [];
  /**
   * Get the event that owns the Exception
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function event(): BelongsTo
  {
    return $this->belongsTo(Event::class);
  }

  public function scopeComposited(Builder $q, $event_id, $old_month)
  {
    $q->where(["event_id" => $event_id, "old_month" => $old_month]);
  }
  


  public  function enable()
  {
    $query= $this->prepare($this->old_month, $this->old_day, $this->new_month, $this->new_day);
    EventOrganization::where('event_id', $this->event_id)
      ->whereMonth("deadline", $this->old_month)
      ->whereDay("deadline", $this->old_day)
      ->update([
        "deadline" =>DB::raw($query->deadline),
        "notification_date" =>DB::raw($query->notification_date)
      ]);
  }

  public function disable()
  {
    $query = $this->prepare($this->new_month, $this->new_day,$this->old_month, $this->old_day);

    EventOrganization::where('event_id', $this->event_id)
      ->whereMonth("deadline", $this->new_month)
      ->whereDay("deadline", $this->new_day)
      ->update([
        "deadline" => DB::raw($query->deadline),
        "notification_date" => DB::raw($query->notification_date)
      ]);
  }

  public function disableAndDelete()
  {
    $this->disable();
    return  $this->delete();
  }


  public static function prepare($old_month,$old_day,$new_month,$new_day)
  {
    $day = strlen($new_day) == 1 ?  sprintf('%02d', $new_day) : $new_day;
    $month = strlen($new_month) == 1 ?  sprintf('%02d', $new_month) : $new_month;

    $notification_date = new stdClass;
    $notification_date->dayDiff =  $new_day - $old_day;
    $notification_date->monthDiff = $new_month - $old_month;

    return  $query = (object) [
      "deadline" => "DATE(CONCAT(YEAR(deadline),'$month','$day'))",
      "notification_date" => " DATE_ADD(DATE_ADD(notification_date,INTERVAL $notification_date->monthDiff MONTH),INTERVAL $notification_date->dayDiff DAY)"
    ];
  }

}
