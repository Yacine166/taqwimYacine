<?php

namespace App\Console;

use App\Jobs\NewYearAttachEvents;
use App\Jobs\NotifyUsers;
use App\Jobs\UpdateNotificationDates;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
  /**
   * Define the application's command schedule.
   */
  protected function schedule(Schedule $schedule): void
  {

    $schedule->job(NewYearAttachEvents::class)->yearly();

    $schedule->job(UpdateNotificationDates::class)->daily();

    $schedule->job(NotifyUsers::class)->dailyAt(today()->hour(8)->toTimeString());

  }
  /**
   * Register the commands for the application.
   */
  protected function commands(): void
  {
    $this->load(__DIR__ . '/Commands');

    require base_path('routes/console.php');
  }
}
