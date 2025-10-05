<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplyLeaveRequest extends FormRequest
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
            'leave_category_id' => 'required|string|exists:leave_categories,id',
            'date' => 'required|date|after_or_equal:today',
            'description' => 'nullable|string|max:1000',
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
            'leave_category_id.required' => 'Leave category is required',
            'leave_category_id.exists' => 'Invalid leave category',
            'date.required' => 'Leave date is required',
            'date.after_or_equal' => 'Leave date cannot be in the past',
            'description.max' => 'Description cannot exceed 1000 characters',
        ];
    }
}

