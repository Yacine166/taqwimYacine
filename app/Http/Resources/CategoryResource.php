<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
  public static $wrap = 'category';
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'name' =>auth()->user()->locale == 'fr' ? $this->name_fr :
      ( auth()->user()->locale == 'ar' ? $this->name_ar:$this->name ),
      'color' => $this->color,
      'image' => $this->image,
      'is_notifable' => $this->whenPivotLoaded('category_organization', function () {
        return $this->pivot->is_notifable;
      }),
      'days_before_notification' => $this->whenPivotLoaded('category_organization', function () {
        return $this->pivot->days_before_notification;
      }),
      'events_count' => $this->events_count ?? $this->whenLoaded('events', fn () => $this->events()->count()),
      'fr' => [
        'name' => $this->name_fr,
      ],
      'ar' => [
        'name' => $this->name_ar,
      ],
    ];
  }
}
