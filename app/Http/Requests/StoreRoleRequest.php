<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreRoleRequest - Validation for creating roles
 */
class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled in controller
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:1000',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'features' => 'nullable|array',
            'features.*' => 'string',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:branches,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Role name is required',
            'name.unique' => 'A role with this name already exists',
            'name.max' => 'Role name must not exceed 255 characters',
            'description.max' => 'Description must not exceed 1000 characters',
            'permissions.*.exists' => 'One or more selected permissions do not exist',
            'branch_ids.*.exists' => 'One or more selected branches do not exist',
        ];
    }
}
