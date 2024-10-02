<?php

namespace App\Nova\Actions;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Laravel\Nova\Actions\Action;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Queue\InteractsWithQueue;
use Laravel\Nova\Http\Requests\NovaRequest;

class AttachEvents extends Action
{
  public $name = 'Attach All Events';
  public $showInline = true;
  public $confirmText = 'keeping qualified & adding newely qualified events. Continue ?';
  use InteractsWithQueue, Queueable;

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
    if ($events->isEmpty()) return Action::danger('Action Cancelled, There is No Events !');
    foreach ($models as $model) {
      $model->attachEvents($events->load('parameters'));
    }
    return Action::message('Events Attached Successfully');
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
