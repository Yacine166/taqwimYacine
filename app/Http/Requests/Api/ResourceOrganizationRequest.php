<?php

namespace App\Http\Requests\Api;

use App\Models\Parameter;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class ResourceOrganizationRequest extends FormRequest
{
  /**
   * Indicates if the validator should stop on the first rule failure.
   * @var bool
   */

  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
   */
  public function rules(): array
  {
    $params = Parameter::all();
    $params_validation_args = [];
    foreach ($params as $param) {
      $params_validation_args[$param->slug] = ['required',Rule::in(range(0,count($param->options) - 1))];
    }
    return   [
      'name' => ['required', Rule::unique('organizations', 'name')
          ->where(function ($query) {
            return $query->where('user_id', auth()->user()->id);
          })->ignore($this->organization)
      ],
      ...$params_validation_args
    ];
  }

  /**
   * Get the error messages for the defined validation rules.
   * @return array<string, string>
   */
  public function messages(): array
  {
    return [
      'unique' => "you already have an organization named :input"
    ];
  }
}
