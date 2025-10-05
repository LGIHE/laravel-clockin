<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttendanceRequest extends FormRequest
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
            'in_time' => 'nullable|date',
            'in_message' => 'nullable|string|max:500',
            'out_time' => 'nullable|date|after:in_time',
            'out_message' => 'nullable|string|max:500',
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
            'in_time.date' => 'Clock in time must be a valid date.',
            'out_time.date' => 'Clock out time must be a valid date.',
            'out_time.after' => 'Clock out time must be after clock in time.',
            'in_message.string' => 'The clock in message must be a string.',
            'in_message.max' => 'The clock in message may not be greater than 500 characters.',
            'out_message.string' => 'The clock out message must be a string.',
            'out_message.max' => 'The clock out message may not be greater than 500 characters.',
        ];
    }
}
