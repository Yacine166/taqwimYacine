<?php

namespace App\Nova\Actions;

use App\Models\Organization;
use Illuminate\Bus\Queueable;

use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Queue\InteractsWithQueue;
use Laravel\Nova\Actions\DestructiveAction;
use Laravel\Nova\Http\Requests\NovaRequest;

class RefreshInstances extends DestructiveAction
{
  public $name = 'Refresh For All Organizations';
  public $showInline = true;
  public $confirmText = 'Delete from unqualified organizations, keep to qualified & attach to newely qualified. Continue ?';
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
    $organizations=Organization::all()->load('parameters');
    if ($organizations->isEmpty()) return DestructiveAction::danger('Action Cancelled, There is No Organizations !');

    foreach ($models as $model) {
      $model->attachOrganizations($organizations, true);
    }

    return DestructiveAction::message('Attached & Dettached to Organizations Successfully');
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