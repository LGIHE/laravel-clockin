<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLeaveCategoryRequest extends FormRequest
{
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('leave_categories', 'name')->ignore($this->route('leave_category')),
            ],
            'max_in_year' => 'required|integer|min:1|max:365',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Leave category name is required',
            'name.unique' => 'A leave category with this name already exists',
            'max_in_year.required' => 'Maximum days per year is required',
            'max_in_year.min' => 'Maximum days per year must be at least 1',
            'max_in_year.max' => 'Maximum days per year cannot exceed 365',
        ];
    }
}
