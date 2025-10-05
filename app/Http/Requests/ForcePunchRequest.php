<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForcePunchRequest extends FormRequest
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
            'user_id' => 'required|string|exists:users,id',
            'type' => 'required|in:in,out',
            'time' => 'required|date',
            'message' => 'nullable|string|max:500',
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
            'user_id.required' => 'User ID is required.',
            'user_id.exists' => 'The selected user does not exist.',
            'type.required' => 'Punch type is required.',
            'type.in' => 'Punch type must be either "in" or "out".',
            'time.required' => 'Time is required.',
            'time.date' => 'Time must be a valid date.',
            'message.string' => 'The message must be a string.',
            'message.max' => 'The message may not be greater than 500 characters.',
        ];
    }
}
