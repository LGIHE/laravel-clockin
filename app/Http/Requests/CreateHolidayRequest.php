<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateHolidayRequest extends FormRequest
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
            'date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $exists = \App\Models\Holiday::whereDate('date', $value)
                        ->whereNull('deleted_at')
                        ->exists();
                    
                    if ($exists) {
                        $fail('A holiday already exists for this date');
                    }
                },
            ],
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
            'date.required' => 'Holiday date is required',
            'date.date' => 'Holiday date must be a valid date',
            'date.unique' => 'A holiday already exists for this date',
        ];
    }
}
