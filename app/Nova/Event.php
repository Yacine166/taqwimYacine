<?php

namespace App\Nova;

use App\Enums\Operator;
use App\Models\Parameter;
use Laravel\Nova\Fields\ID;
use App\Models\Organization;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\BelongsTo;
use App\Nova\Actions\AttachInstances;
use App\Nova\Actions\DetachInstances;
use Laravel\Nova\Actions\ExportAsCsv;
use App\Nova\Actions\RefreshInstances;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Nova\Actions\NotifyUserAboutNextEvent;
use App\Nova\Organization as NovaOrganization;

class Event extends Resource
{
    
  /**
   * The model the resource corresponds to.
   *
   * @var class-string<\App\Models\Event>
   */
  public static $model = \App\Models\Event::class;

  /**
   * The single value that should be used to represent the resource when being displayed.
   *
   * @var string
   */
  public static $title = 'id';
  public static $priority = 1;

  /**
   * The columns that should be searched.
   *
   * @var array
   */
  public static $search = [
    'id','name'
  ];

  /**
   * Get the fields displayed by the resource.
   *
   * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
   * @return array
   */
  public function fields(NovaRequest $request)
  {
    return [
      ID::make()->sortable(),
      BelongsTo::make('Category')->filterable(),
      Text::make('Name')->rules('required'),
      Text::make('Name Ar')->rules('required'),
      Text::make('Name Fr')->rules('required'),
      Textarea::make('Details')->rules('required'),
      Textarea::make('Details Fr')->rules('required'),
      Textarea::make('Details Ar')->rules('required'),
      Select::make('month', 'cycle_month')
        ->displayUsing(fn ($option) => $option == "monthly" ? "monthly" : now()->month($option)->format('F'))
        ->options(collect(["monthly", ...range(1, 12, 1)])->mapWithKeys(function ($option, $key) {
          return [$option => $option == "monthly" ? "monthly" : now()->month($option)->format('F')];
        }))
        ->rules('required')
        ->hideWhenUpdating()
        ->filterable()
        ->displayUsingLabels(),

      Select::make('month', 'cycle_month')
        ->displayUsing(fn ($option) => $option == "monthly" ? "monthly" : now()->month($option)->format('F'))
        ->dependsOn(
          "cycle_month",
          function (Select $field, NovaRequest $request, FormData $formData) {
            $field->options(function () use ($formData) {
              return $formData->cycle_month == "monthly" ?
                ["monthly" => "monthly"]
                : collect([...range(1, 12, 1)])->mapWithKeys(function ($option, $key) {
                  return [$option =>  now()->month($option)->format('F')];
                });
            });
          }
        )

        ->rules('required')
        ->hideFromIndex()
        ->hideFromDetail()
        ->hideWhenCreating()
        ->showOnUpdating()
        ->displayUsingLabels(),

      Select::make('Day Of The Month', "cycle_day")
        ->displayUsing(fn ($day) => now()->month(1)->day($day)->format('jS'))
        ->dependsOn(['cycle_month'], function (Select $field, NovaRequest $request, FormData $formData) {
          $options = range(1, now()->month($formData->cycle_month == "monthly" ? 2 : $formData->cycle_month)->daysInMonth, 1);
          $field
            ->options(collect($options)->mapWithKeys(function ($option, $key) {
              return [$option => $option];
            }))
            ->displayUsingLabels();
        })
        ->rules('required'),

      Select::make('Every', "cycle")
        ->hideWhenUpdating()
        ->displayUsing(fn ($option) => $option == 1 ? "$option month" : ($option == 12 ? "once a year" : "$option months"))
        ->dependsOn(['cycle_month'], function (Select $field, NovaRequest $request, FormData $formData) {
          if ($formData->cycle_month == "monthly") {
            $field->options(collect(range(1, 11, 1))->mapWithKeys(function ($option, $key) {
              $formated_option = $option == 1 ? "$option month" : "$option months";
              return [$option => $formated_option];
            }));
          } else {
            $field
              ->options(["12" => "once this year"])
              ->withMeta(['value' => "12"]);
          }
          $field
            ->rules('required')
            ->displayUsingLabels();
        })
        ,

      BelongsToMany::make('Parameters')
        ->fields(function () {
          $parameter = null;
          return [
            Select::make('Condition', 'condition')

              ->dependsOn(
                ['parameters'],
                function (Select $field, NovaRequest $request, FormData $formData) {
                  $parameter_id = $formData?->parameters ?: $formData->{"resource:parameters"};
                  $parameter = Parameter::find($parameter_id);
                  $readOnly = count($parameter?->options ?: []) == 2;
                  $conditions_formated = [];
                  $conditions = [];

                  if ($readOnly) {
                    $conditions =   [Operator::EQUAL->value => Operator::EQUAL->value];
                    $field->default(Operator::EQUAL->value);
                  }

                  collect(Operator::cases())
                    ->each(function ($value, $key) use (&$conditions_formated) {
                      $conditions_formated[$value->value] = $value->value;
                    });

                  $field
                    ->rules('required')
                    ->options($conditions ?: $conditions_formated)
                    ->readonly($readOnly || !$parameter);
                }
              ),

            Select::make('Option', 'value')
              ->dependsOn(
                ['parameters'],
                function (Select $field, NovaRequest $request, FormData $formData) use (&$parameter) {
                  $parameter_id = $formData?->parameters ?: $formData->{"resource:parameters"};
                  $parameter = Parameter::find($parameter_id);
                  $options = $parameter?->options;
                  $options_formated = [];
                  collect($options)->each(function ($value, $key) use (&$options_formated) {
                    $options_formated[$value] = $value;
                  });

                  $field
                    ->rules('required')
                    ->options($options_formated)
                    ->readonly(!$parameter);
                }
              ),
          ];
        }),

      BelongsToMany::make('Instances', 'organizations', NovaOrganization::class)
        ->fields(function () {
          return [
            ID::make('Organization id', 'organization_id'),
            Date::make('Deadline')
              ->displayUsing(fn ($date) => $date?->format("Y-m-d"))
              ->rules('required')
              ->sortable(),

            Date::make('Notification Date')
              ->displayUsing(fn ($date) => $date?->format("Y-m-d"))
              ->sortable(),

            DateTime::make('Completed At')
              ->displayUsing(fn ($date) => $date?->format("Y-m-d"))
              ->sortable()
          ];
        })->allowDuplicateRelations(),

      HasMany::make('Exceptions')
        ->hideWhenUpdating(),
    ];
  }

  public function categories()
  {
    return $this->belongsTo(Category::class);
  }
  public function organizations()
  {
    return $this->belongsToMany(Organization::class)
      ->withPivot('id', 'completed_at', 'deadline', 'notification_date');
  }
  public function parameters()
  {
    return $this->belongsToMany(Parameter::class)
      ->withPivot('condition', 'value', 'parameter_id');
  }

  /**
   * Get the actions available for the resource.
   *
   * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
   * @return array
   */
  public function actions(NovaRequest $request)
  {
    return [
      new NotifyUserAboutNextEvent,
      new AttachInstances,
      new RefreshInstances,
      new DetachInstances,
      ExportAsCsv::make()->nameable(),
    ];
  }
}
