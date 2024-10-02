<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParameterResource extends JsonResource
{
  public static $wrap="parameter";
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
          'id'=>$this->id,
           'name'=>auth()->user()->locale == 'fr' ? $this->name_fr : (auth()->user()->locale == 'ar' ? $this->name_ar : $this->name),
          'slug'=>$this->slug,
          'options'
      => (object)  (auth()->user()->locale == 'fr' ? $this->options_fr : (auth()->user()->locale == 'ar' ?  $this->options_ar : $this->options)),
          'fr'=>[
            'name'=>$this->name_fr,
            'options'=>$this->options_fr,
          ],
          'ar'=>[
            'name'=>$this->name_ar,
            'options'=>$this->options_ar,
          ],
        ];
    }
}
