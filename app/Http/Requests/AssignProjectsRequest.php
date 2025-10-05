<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignProjectsRequest extends FormRequest
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
            'project_ids' => 'required|array',
            'project_ids.*' => 'string|exists:projects,id',
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
            'project_ids.required' => 'Project IDs are required',
            'project_ids.array' => 'Project IDs must be an array',
            'project_ids.*.exists' => 'One or more project IDs are invalid',
        ];
    }
}

