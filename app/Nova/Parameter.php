<?php

namespace App\Nova;

use App\Enums\Operator;
use Laravel\Nova\Fields\ID;
use App\Models\Organization;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\MultiSelect;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Http\Requests\NovaRequest;

class Parameter extends Resource
{
  public static $priority = 4;
  /**
   * The model the resource corresponds to.
   *
   * @var class-string<\App\Models\Parameter>
   */
  public static $model = \App\Models\Parameter::class;

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

      Text::make('Name')->sortable()->rules('required'),

      Text::make('Name Fr')->onlyOnForms()->rules('required')->showOnDetail(),

      Text::make('Name Ar')->onlyOnForms()->rules('required')->showOnDetail(),

      Slug::make('slug')->from('name')->separator("_")->rules('required'),

      MultiSelect::make('Options')
        ->options(function () {
          $optionsGlobal = [];
          $parameters = Parameter::pluck('options', 'name');
          foreach ($parameters as $name => $options) {
            foreach ($options as $index => $option) {
              $optionsGlobal[$option]  = ['label' => $option, 'group' => $name];
            }
          }
          return $optionsGlobal;
        })
        ->hideWhenCreating()
        ->hideWhenUpdating()
        ->displayUsingLabels(),
      MultiSelect::make('Options Fr')
        ->options(function () {
          $optionsGlobal = [];
          $parameters = Parameter::pluck('options_fr', 'name');
          foreach ($parameters as $name => $options) {
            foreach ($options as $index => $option) {
              $optionsGlobal[$option]  = ['label' => $option, 'group' => $name];
            }
          }
          return $optionsGlobal;
        })
        ->hideWhenCreating()
        ->hideWhenUpdating()
        ->hideFromIndex()
        ->displayUsingLabels(),
      MultiSelect::make('Options Ar')
        ->options(function () {
          $optionsGlobal = [];
          $parameters = Parameter::pluck('options_ar', 'name');
          foreach ($parameters as $name => $options) {
            foreach ($options as $index => $option) {
              $optionsGlobal[$option]  = ['label' => $option, 'group' => $name];
            }
          }
          return $optionsGlobal;
        })
        ->hideWhenCreating()
        ->hideWhenUpdating()
        ->hideFromIndex()
        ->displayUsingLabels(),


      Text::make('Options')
        ->fillUsing(function ($request, $model, $attribute, $requestAttribute) {
          $input = $request->input($attribute);

          $input_array = array_filter(explode(',', $input));

          foreach ($input_array as $index => &$option) {
            $option = trim($option);
          }
          $input = implode(',', $input_array);
          $model->{$attribute} = json_decode('["' . str_replace(',', '","', $input) . '"]');
        })
        ->onlyOnForms()
        ->rules('required'),

      Text::make('Options Fr')
        ->fillUsing(function ($request, $model, $attribute, $requestAttribute) {

          $input = $request->input($attribute);

          $input_array = array_filter(explode(',', $input));

          foreach ($input_array as $index => &$option) {
            $option = trim($option);
          }
          $input = implode(',', $input_array);
          $model->{$attribute} = json_decode('["' . str_replace(',', '","', $input) . '"]');
          $this->validateOptions($model, $attribute);
        })
        ->onlyOnForms()
        ->rules('required'),
      Text::make('Options Ar')
        ->fillUsing(function ($request, $model, $attribute, $requestAttribute) {
          $input = $request->input($attribute);

          $input_array = array_filter(explode(',', $input));
          if(count($input_array)!=count($model->options)){
            $input_array = array_filter(explode('،', $input));
          }

          foreach ($input_array as $index => &$option) {
            $option = trim($option);
          }
          $input = implode(',', $input_array);
          $model->{$attribute} = json_decode('["' . str_replace(',', '","', $input) . '"]');
          $this->validateOptions($model, $attribute);
        })
        ->onlyOnForms()
        ->rules('required'),
      BelongsToMany::make('Organizations')
        ->fields(function () {
          return [
            Select::make('Option', 'option')
              ->dependsOn(['organizations'], function (Select $field, NovaRequest $request, FormData $formData) {
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
    ];
  }
  public function organizations()
  {
    return $this->belongsToMany(Organization::class)
      ->withPivot('id', 'option');
  }
  public function events()
  {
    return $this->belongsToMany(Event::class)
      ->withPivot('condition', 'value', 'event_id');
  }
  public function validateOptions($model,$attribute){
    if (count($model->{$attribute}) != count($model->options)) {
      throw \Illuminate\Validation\ValidationException::withMessages([$attribute => ['The number of options does not equal "Options"']]);
    }
  }

  public function actions(NovaRequest $request)
  {
    return [
        ExportAsCsv::make()->nameable(),
    ];
  }
}
