<?php

namespace App\Nova\Actions;

use App\Models\Event;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Actions\DestructiveAction;
use Laravel\Nova\Http\Requests\NovaRequest;

class RefreshEvents extends DestructiveAction
{
  public $name = 'Refresh All Events';
  public $showInline = true;
  public $confirmText = 'Deleting unqualified , keeping qualified & adding newely qualified events. Continue ?';
    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
    $events = Event::all();
    if ($events->isEmpty()) return DestructiveAction::danger('Action Cancelled, There is No Events !');
    foreach ($models as $model) {
      $model->attachEvents($events->load('parameters'),true);
    }
    return DestructiveAction::message('Events Attached & Detached Successfully');
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
