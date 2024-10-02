<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
   */
  public function rules(): array
  {
    return
      [
        'password' => ['required', 'current_password'],
        'name'     => ['required', 'string', 'max:255'],
        'phone'    => ['string', 'nullable'],
        'email'    => [
        'required', 'string', 'email', 'max:255',

          Rule::unique('users', 'email')
            ->where(function ($query) {
              return $query->get();
            })->ignore(auth()->user()->id)
        ]
      ];
  }
}
