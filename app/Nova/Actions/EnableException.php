<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

class EnableException extends Action
{
  public $name = 'Enable';
  public $showInline = true;
  public $confirmText = 'Edit Previous And Future Deadlines. Continue ?';
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
    $models->each(fn ($model) => $model->enable());
    return Action::message('Enabled Successfully');
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
