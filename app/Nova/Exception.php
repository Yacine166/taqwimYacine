<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use App\Models\EventOrganization;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\BelongsTo;
use Illuminate\Support\Facades\Log;
use App\Models\Exception as ExceptionModel;
use Laravel\Nova\Http\Requests\NovaRequest;

class Exception extends Resource
{
  /**
   * The model the resource corresponds to.
   *
   * @var class-string<\App\Models\Exception>
   */
  public static $model = \App\Models\Exception::class;

  /**
   * The single value that should be used to represent the resource when being displayed.
   *
   * @var string
   */
  public static $title = 'label';

  /**
   * The columns that should be searched.
   *
   * @var array
   */
  public static $search = [
    'event_id', 'label'
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
      ID::make(),
      Text::make('label', 'label')
        ->rules('required')
        ->sortable(),

      BelongsTo::make('event')
        ->hideWhenUpdating(),

      Select::make('Old month')
        ->displayUsing(fn ($option) => $this->monthFormat($option))
        ->dependsOn('event', function (Select $field, NovaRequest $request, FormData $formData) {
          $deadlines_months =  EventOrganization::where("event_id", $formData->event)
            ->pluck('deadline')->unique()
            ->map(fn ($deadline) => $deadline->month);
          $field
            ->options($this->Monthslist($deadlines_months))
            ->default($deadlines_months->first());
        })
        ->hideWhenUpdating()
        ->rules('required'),

      Select::make('Old day')
        ->displayUsing(fn ($day) => $this->dayFormat($day))
        ->dependsOn(
          ['old_month', 'event'],
          function (Select $field, NovaRequest $request, FormData $formData) {
            $event_id =$formData->event;
            $old_month =$formData->old_month;
            $setup = $this->setup($field, $event_id, $old_month);

            $field
              ->options($this->days($setup->available_days, $setup->exceptional_days, $setup->days))
              ->displayUsingLabels();

          }
        )
        ->hideWhenUpdating()
        ->rules('required'),

      Select::make('New month')
        ->displayUsing(fn ($option) => $this->monthFormat($option))
        ->dependsOn(
          ['event', 'old_month'],
          function (Select $field, NovaRequest $request, FormData $formData) {
            $deadlines_months =  EventOrganization::where("event_id", $formData->event)
              ->pluck('deadline')->unique()
              ->map(fn ($deadline) => $deadline->month);
            $field
              ->options($this->Monthslist($deadlines_months))
              ->default($formData->old_month);
          }
        )
        ->hideWhenUpdating()
        ->rules('required'),

      Select::make('New day')
        ->displayUsing(fn ($day) => $this->dayFormat($day))

        ->dependsOn(
          ['new_month', 'event'],
          function (Select $field, NovaRequest $request, FormData $formData) {
            $event_id = $formData->event;
            $new_month =  $formData->new_month;
            $setup = $this->setup($field, $event_id, $new_month);

            $field
              ->options($this->days($setup->available_days, $setup->exceptional_days, $setup->days))
              ->displayUsingLabels();
          }
          
        )
        ->hideWhenUpdating()
        ->rules('required'),
    ];
  }

  public static function afterCreate(NovaRequest $request,  $exception)
  {
    $exception->enable();
  }

  public static function afterDelete(NovaRequest $request,  $exception)
  {
    $exception->disable();
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
      new Actions\EnableException,
      new Actions\DisableException,
    ];
  }


  public static function days($available_days, $exceptional_days, $options)
  {

    return collect($options)->mapWithKeys(function ($option) use ($available_days, $exceptional_days) {
      if ($exceptional_days?->contains($option)) return [];
      $exists =  $available_days->contains($option) ? "Deadline Found:" : "Free Days(Exceptions excluded):";
      return [$option => ["label" => "$option", "group" => $exists]];
    });
  }

  public  function setup($field, $event_id, $month)
  {
    $deadlines = EventOrganization::where("event_id", $event_id)
      ->whereMonth("deadline", $month)
      ->pluck("deadline")->unique();

    $available_days = $deadlines->map(fn ($deadline) => $deadline->day);
    $event_exceptions = ExceptionModel::where("event_id", $event_id)->get();

    $exceptional_days = $event_exceptions
      ->where('old_month', $month)
      ->pluck('old_day')
      ->unique();

    $exceptional_days
      ->push(...$event_exceptions->where('new_month', $month)->pluck('new_day')->unique());

    if ($available_days->isNotEmpty())
      $field
        ->default(
          $available_days
            ->where(function ($day) use ($exceptional_days) {
              return !$exceptional_days->contains($day);
            })->first()
        );

    $days = range(1, now()->month($month)->daysInMonth, 1);
    return (object)[
      "days" => $days,
      "available_days" => $available_days,
      "exceptional_days" => $exceptional_days,
    ];
  }

  public  function Monthslist($deadlines_months)
  {
    return  collect([...range(1, 12, 1)])
      ->mapWithKeys(
        function ($option) use ($deadlines_months) {
          $exists =  $deadlines_months->contains($option) ? "Deadline Found (Exceptions included) :" : "";
          return [$option => ["label" => $this->monthFormat($option), "group" => $exists]];
        }
      );
  }

  public function monthFormat($option)
  {
    return now()->month($option)->format('F');
  }

  public function dayFormat($day)
  {
    return  now()->month(1)->day($day)->format('jS');
  }
}
