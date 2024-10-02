<?php

namespace App\Nova\Actions;

use App\Models\Organization;
use Illuminate\Bus\Queueable;
use Laravel\Nova\Actions\Action;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Laravel\Nova\Http\Requests\NovaRequest;

class AttachInstances extends Action
{
  public $name = 'Attach For All Organizations';
  public $showInline = true;
  public $confirmText = 'keep to qualified & attach to newely qualified. Continue ?';
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
    $organizations = Organization::all()->load('parameters');
    if ($organizations->isEmpty()) return Action::danger('Action Cancelled, There is No Organizations !');

    foreach ($models as $model) {
      $model->attachOrganizations($organizations, true);
    }

    return Action::message('Attached to Organizations Successfully');
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
