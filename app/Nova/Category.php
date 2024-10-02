<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use App\Models\Organization;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Color;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Http\Requests\NovaRequest;

class Category extends Resource
{
  /**
   * The model the resource corresponds to.
   *
   * @var class-string<\App\Models\Category>
   */
  public static $model = \App\Models\Category::class;

  /**
   * The single value that should be used to represent the resource when being displayed.
   *
   * @var string
   */
  public static $title = 'name';
  public static $priority = 5;

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
      Text::make('name')->rules('required'),
      Text::make('name_fr')->rules('required'),
      Text::make('name_ar')->rules('required'),
      Color::make('Color')->rules('required'),
      Image::make('image')->disk('public'),
      HasMany::make('Events'),

      BelongsToMany::make('Notifications', 'organizations', \App\Nova\Organization::class)
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


  public function events()
  {
    return $this->hasMany(Event::class);
  }

  public function organizations()
  {
    return $this->belongsToMany(Organization::class)
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
    $model->organizations()->attach(Organization::all());
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
        ExportAsCsv::make()->nameable(),
    ];
  }
}
