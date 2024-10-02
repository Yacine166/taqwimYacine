<?php

namespace App\Nova;


use App\Models\Event;
use App\Nova\Actions;
use App\Models\Category;
use App\Models\Parameter;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Text;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Http\Requests\NovaRequest;

class Organization extends Resource
{
  public static $priority = 3;

  /**
   * The model the resource corresponds to.
   *
   * @var class-string<\App\Models\Organization>
   */
  public static $model = \App\Models\Organization::class;

  /**
   * The single value that should be used to represent the resource when being displayed.
   *
   * @var string
   */
  public static $title = 'name';
  /**
   * The columns that should be searched.
   *
   * @var array
   */
  public static $search = [
    'id',
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
      BelongsTo::make('User'),
      Text::make('name')
        ->sortable()
        ->dependsOn("user", function (Text $field, NovaRequest $request, FormData $formData) {
          $field->rules([
            'required', Rule::unique('organizations', 'name')
              ->where(function ($query) use ($formData) {
                return $query->where('user_id', $formData->user);
              })->ignore($this->organization)
          ]);
        }),


      BelongsToMany::make('Parameters')
        ->fields(function () {
          return [
            Select::make('Option', 'option')
              ->dependsOn(['parameters'], function (Select $field, NovaRequest $request, FormData $formData) {
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
                  ->displayUsingLabels();
              })
          ];
        }),

      BelongsToMany::make('Events')
        ->fields(function () {
          return [
            ID::make('event id')
              ->sortable(),
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


      BelongsToMany::make('Notifications', 'categories', \App\Nova\Category::class)
        ->fields(function () {
          return [
            Boolean::make('notifable', 'is_notifable')->default(true),
            Number::make('notify me before', 'days_before_notification')
              ->displayUsing(fn ($days) => $days != 1 ? "$days days" : "$days day")
              ->rules('required')
              ->default(15)
          ];
        }),
    ];
  }


  public function parameters()
  {
    return $this->belongsToMany(Parameter::class)
      ->withPivot('id', 'option');
  }
  public function events()
  {
    return $this->belongsToMany(Event::class)
      ->withPivot('id', 'completed_at', 'deadline', 'notification_date');
  }
  public function categories()
  {
    return $this->belongsToMany(Category::class)
      ->withPivot('id', 'is_notifable', 'days_before_notification');
  }

  /**
   * Register a callback to be called after the resource is created.
   *
   * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
   * @param  \Illuminate\Database\Eloquent\Model  $model
   * @return void
   */
  public static function afterCreate(NovaRequest $request, $model)
  {
    $model->categories()->attach(Category::all());
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
      new Actions\AttachEvents,
      new Actions\RefreshEvents,
      new Actions\DetachEvents,
    ];
  }
}
