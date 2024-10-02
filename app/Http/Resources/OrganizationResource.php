<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\EventResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\OrganizationParameterCollection;


class OrganizationResource extends JsonResource
{
  public static $wrap = 'organization';
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    $parameters=$this->whenLoaded('parameters', function (){
      $params=collect(new OrganizationParameterCollection($this->parameters))->map(function ($param) {
        return  (array)$param;
      });
      foreach ($params as $param) {
        $parameters[$param['slug']]= $param['option'];
      }
      return $parameters;
    })?:[];

    return [
      'id' => $this->id,
      'name' => $this->name,
      $this->whenLoaded('parameters',function() use ($parameters){
        return $this->merge($parameters);
      }),
      'events' => EventResource::collection($this->whenLoaded('events')),
      $this->whenLoaded('parameters',function() use ($parameters){ return $this->merge($parameters);}),
      'deadline' => $this->whenPivotLoaded('event_organization', function () {
        return $this->pivot->deadline;
      }),
      'completed_at' => $this->whenPivotLoaded('event_organization', function () {
        return $this->pivot->completed_at;
      }),
      'pivot_id' => $this->whenPivotLoaded('event_organization', function () {
        return $this->pivot->id;
      }),
    ];
  }
}
