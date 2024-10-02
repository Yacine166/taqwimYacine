<?php

namespace App\Http\Requests\Api;

use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
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
        return [
      'current_password' => ['required', 'current_password'],
      'password' => [
        'required', 'confirmed', Password::defaults()
      ],
    ];
    }
}
