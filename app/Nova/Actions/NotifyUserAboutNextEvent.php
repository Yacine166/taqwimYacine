<?php

namespace App\Nova\Actions;

use App\Models\EventOrganization;
use Illuminate\Bus\Queueable;
use App\Jobs\NotifySingleUser;
use Laravel\Nova\Actions\Action;
use Illuminate\Support\Collection;
use App\Jobs\UpdateNotificationDates;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Laravel\Nova\Http\Requests\NovaRequest;

class NotifyUserAboutNextEvent extends Action implements ShouldQueue
{
    use InteractsWithQueue, Queueable;

    public $name = 'Notify users about next deadline';
    public $showInline = true;
    public $confirmText = 'This will notify eligable users for notifications about the next deadline of this event. Continue ?';

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        UpdateNotificationDates::dispatch();

        foreach ($models as $event) {

            $eventInstances = EventOrganization::where('event_id',$event->id)
            ->where('event_organization.deadline','=', function($q){
               return $q->from('event_organization')->select('deadline')->where('deadline','>=',now())->limit(1);
            })
            ->whereNull('event_organization.completed_at')
            ->get();

            foreach ($eventInstances as $eventInstance) {
                NotifySingleUser::dispatch($eventInstance);
            }
        }
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [];
    }
}
