<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Illuminate\Validation\Rules;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\PasswordConfirmation;

class User extends Resource
{
  public static $priority = 2;

  /**
   * The model the resource corresponds to.
   *
   * @var class-string<\App\Models\User>
   */
  public static $model = \App\Models\User::class;

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
    'id', 'name', 'email',
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

      Text::make('Name')
        ->sortable()
        ->rules('required', 'max:255'),

      Text::make('Email')
        ->sortable()
        ->rules('required', 'email', 'max:254')
        ->creationRules('unique:users,email')
        ->updateRules('unique:users,email,{{resourceId}}'),
      Text::make('Phone'),
      Boolean::make("admin",'is_admin'),
      Password::make('Password')
        ->onlyOnForms()
        ->creationRules('required', 'confirmed', Rules\Password::defaults())
        ->updateRules('nullable', 'confirmed', Rules\Password::defaults()),

      PasswordConfirmation::make('Password Confirmation'),

      HasMany::make('Organizations'),
    ];
  }


  /**
   * Get the cards available for the request.
   *
   * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
   * @return array
   */
  public function cards(NovaRequest $request)
  {
    return [];
  }

  /**
   * Get the filters available for the resource.
   *
   * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
   * @return array
   */
  public function filters(NovaRequest $request)
  {
    return [];
  }

  /**
   * Get the lenses available for the resource.
   *
   * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
   * @return array
   */
  public function lenses(NovaRequest $request)
  {
    return [];
  }

  /**
   * Get the actions available for the resource.
   *
   * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
   * @return array
   */
  public function actions(NovaRequest $request)
  {
    return [];
  }
}
