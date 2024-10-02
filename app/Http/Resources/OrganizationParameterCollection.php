<?php

namespace App\Http\Resources;

use App\Models\OrganizationParameter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationParameterCollection extends JsonResource
{
  public static $wrap = 'parameters';
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [$this->merge(OrganizationParameterResource::collection($this)->toArray($request))];
  }
}