<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:6',
            'user_level_id' => 'required|string|exists:user_levels,id',
            'designation_id' => 'nullable|string|exists:designations,id',
            'department_id' => 'nullable|string|exists:departments,id',
            'status' => 'nullable|integer|in:0,1',
            'project_ids' => 'nullable|array',
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
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'email.unique' => 'Email already exists',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
            'user_level_id.required' => 'User level is required',
            'user_level_id.exists' => 'Invalid user level',
            'designation_id.exists' => 'Invalid designation',
            'department_id.exists' => 'Invalid department',
            'status.in' => 'Status must be 0 or 1',
            'project_ids.*.exists' => 'One or more project IDs are invalid',
        ];
    }
}

