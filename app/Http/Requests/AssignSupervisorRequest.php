<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignSupervisorRequest extends FormRequest
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
        $userId = $this->route('id');
        
        return [
            'supervisor_ids' => 'nullable|array',
            'supervisor_ids.*' => [
                'string',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($userId) {
                    if ($value === $userId) {
                        $fail('A user cannot supervise themselves.');
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
            'supervisor_ids.*.exists' => 'One or more supervisor IDs are invalid',
            'supervisor_ids.array' => 'Supervisor IDs must be an array',
        ];
    }
}

