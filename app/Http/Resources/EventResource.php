<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
  public static $wrap = 'event';
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'name' =>
        auth()->user()->locale == 'fr' ? $this->name_fr : (auth()->user()->locale == 'ar' ? $this->name_ar : $this->name),
      'details' =>
      auth()->user()->locale == 'fr' ? $this->details_fr : (auth()->user()->locale == 'ar' ? $this->details_ar : $this->details),
      'cycle' => $this->cycle=="monthly" ? "monthly" : "yearly",
      'category' => new CategoryResource($this->category),
      'organizations' => OrganizationResource::collection($this->whenLoaded('organizations')),
      'deadline' => $this->whenPivotLoaded('event_organization', function () {
        return $this->pivot->deadline->format('Y-m-d');
      }),
      'completed_at' => $this->whenPivotLoaded('event_organization', function () {
        return $this->pivot->completed_at?->format("Y-m-d H:i");
      }),
      'id' => $this->whenPivotLoaded('event_organization', function () {
        return $this->pivot->id;
      }),
      'fr' => [
        'name' => $this->name_fr,
        'details' => $this->details_fr,
      ],
      'ar' => [
        'name' => $this->name_ar,
        'details' => $this->details_ar,
      ],
    ];
  }
}
