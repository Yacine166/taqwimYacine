<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use App\Models\EventOrganization;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class NotifyUsers implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  /**
   * Create a new job instance.
   */
  public $events_notifiable_Today;

  public function __construct()
  {

    $this->events_notifiable_Today =
        EventOrganization::where('notification_date', today())
                            ->where('completed_at', null)
                            ->get();
  }

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    $events = $this->events_notifiable_Today;
    foreach ($events as $eventInstance) {
        NotifySingleUser::dispatch($eventInstance);
    }
  }
}
