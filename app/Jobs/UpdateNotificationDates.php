<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Models\EventOrganization;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateNotificationDates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //set dates to null for those who disabled notifs
        DB::table('event_organization')
        ->join('events', 'events.id', '=', 'event_organization.event_id')
        ->join('category_organization', function ($join) {
            $join->on('category_organization.category_id', '=', 'events.category_id')
                ->on('category_organization.organization_id', '=', 'event_organization.organization_id');
        })
        ->where('is_notifable', false)
        ->whereNotNull('notification_date')
        ->update(['notification_date' => null]);
        

        //set dates for those who enabled notifs
        DB::table('event_organization')
        ->join('events', 'events.id', '=', 'event_organization.event_id')
        ->join('category_organization', function ($join) {
            $join->on('category_organization.category_id', '=', 'events.category_id')
                ->on('category_organization.organization_id', '=', 'event_organization.organization_id');
        })
        ->where('is_notifable', true)
        ->whereNull('notification_date')
        ->update(['notification_date'=>DB::raw("DATE_ADD(deadline, INTERVAL -days_before_notification DAY)")]);
    }
}
