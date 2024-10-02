<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\OrganizationCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
  /**
   * The "data" wrapper that should be applied.
   *
   * @var string|null
   */
  public static $wrap = 'user';
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'email' => $this->email,
      'phone' => $this->phone,
      'locale' =>$this->locale,
      'organizations' => new OrganizationCollection($this->whenLoaded('organizations'))
    ];
  }
}
