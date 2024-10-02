<?php

namespace App\Helpers;

use Throwable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class Helper
{
    public static function notificationContentTranslationHelper($locale,$eventInstance):string
    {
        switch ($locale){
            case 'ar':
                return  $eventInstance->event->category->name_ar . PHP_EOL .  PHP_EOL .  $eventInstance->event->name_ar . PHP_EOL .  PHP_EOL .  $eventInstance->event->details_ar;

            case 'en':
                return  $eventInstance->event->category->name . PHP_EOL .  PHP_EOL .  $eventInstance->event->name . PHP_EOL .  PHP_EOL .  $eventInstance->event->details;

            default:
                return  $eventInstance->event->category->name_fr . PHP_EOL .  PHP_EOL .  $eventInstance->event->name_fr . PHP_EOL .  PHP_EOL .  $eventInstance->event->details_fr;
        }
    }

  /**
   * Log database queries with the total count.
   *
   * @param  callable  $DBoperations
   * @return array
   */
  public static function lq(callable $DBoperations): array
  {
    DB::enableQueryLog();
    $DBoperations();
    $log = DB::getQueryLog();
    DB::disableQueryLog();
    DB::flushQueryLog();

    return [
      'count' => count($log),
      'log'   => $log
    ];
  }

  /**
   * Get the closest date in the cycle.
   *
   * @param  date  $date
   * @param  int  $cycle
   * @param  int  $day
   * @return date  $date
   */
  public static function startingDate($now, int $cycle,int $day)
  {
    $cycles = range(1, 12, $cycle);
    $starting_month = [...array_filter($cycles, fn ($cycle) => $day >= $now->day ?  $cycle >= $now->month :  $cycle > $now->month)];
    if ($starting_month) $starting_month = $starting_month[0];

    return $now->day($day)->month($starting_month);
  }

  /**
   * get a Model's Api Resource quickly for debugging, developement and testing only
   *
   * you must follow laravel naming conventions
   *
   * Alert : don't use it for production code
   * @param Model $model
   * @throws ResourceNotFoundException
   * @return JsonResource
   */
  public static function getResource(Model $model): JsonResource
  {
    $class = class_basename($model::class);
    $exp = "return new App\Http\Resources\\$class" . "Resource(\$model);";
    try {

      return  eval($exp);
    } catch (Throwable  $e) {
      throw new ResourceNotFoundException("404 so not found, you probably don't have $class" . "Resource in your App\Http\Resources\n check your Resource's name or create one using ``` php artisan make:resource $class" . "Resource ```");
    }
  }

  /**
   * get a Model's Api collection quickly for debugging, developement and testing only
   *
   * you must follow laravel naming conventions
   *
   * Alert : don't use it for production code
   * @param Collection $collection
   * @throws ResourceNotFoundException
   * @return ResourceCollection
   */
  public static function getCollection(Collection $collection): ResourceCollection
  {
    $class = class_basename($collection->first()::class);
    $exp = "return new App\Http\Resources\\$class" . "Collection(\$collection);";
    try {

      return  eval($exp);
    } catch (Throwable  $e) {
      throw new ResourceNotFoundException("404 so not found, you probably don't have $class" . "Collection or $class" . "Resource in your App\Http\Resources\n check your naming or create one using:\n ``` php artisan make:resource $class" . "Resource ```\n
      ``` php artisan make:resource $class" . "Collection ```");
    }
  }

  /**
   * compare to values and return the evaluation
   *
   *  available $condition values  '=', '<', '>', '<=', '>='
   */
  public static function bool($first_value, $secondValue, string $condition = '='): bool
  {
    $match = false;
    switch (true) {
      case $condition == '=' &&  !$match = $first_value == $secondValue:
        break;
      case $condition == '<' && !$match = $first_value < $secondValue:
        break;
      case $condition == '>' &&  !$match = $first_value > $secondValue:
        break;
      case $condition == '>=' && !$match = $first_value >= $secondValue:
        break;
      case $condition == '<=' && !$match = $first_value <= $secondValue:
        break;
      default:
        break;
    }
    return $match;
  }
  public static function csvToArray($filename = '', $delimiter = ',')
{
    if (!file_exists($filename) || !is_readable($filename))
        throw new \Exception('wrong file');

    $header = null;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== false)
    {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
        {
            if (!$header)
               {
                $header = $row;
                foreach ($header as $index => $column) {
                    $col  = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $column);
                    if($col != '' && isset($col)) $header[$index] = $col;
                    // $header[$index] = $col;
                }
            }
            else
                $data[] = array_combine($header, $row);
        }
        fclose($handle);
    }
    return $data;
}
}
