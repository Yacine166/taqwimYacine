<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationParameterResource extends JsonResource
{
  public static $wrap="organization_parameter";
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       $option_index= array_search($this->pivot->option, $this->options);
        return [ 
        'slug'=>$this->slug,
        'option'=> (int)$option_index
      ];
    }
}
