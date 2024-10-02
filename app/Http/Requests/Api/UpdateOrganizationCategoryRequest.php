<?php

namespace App\Http\Requests\Api;

use App\Models\Category;
use App\Models\Organization;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrganizationCategoryRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return $this->route('category') && $this->user()->can('update', $this->route('organization'));
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
   */
  public function rules(): array
  {
    return [
      'is_notifable' => ["required", "boolean"],
      'days_before_notification' => ["required", "integer", "min:0", "max:60"]
    ];
  }
}
