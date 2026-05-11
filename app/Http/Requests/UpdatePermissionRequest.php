<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdatePermissionRequest - Validation for updating permissions
 */
class UpdatePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled in controller
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:permissions,name,' . $this->permission->id,
            'name_ar' => 'nullable|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Permission name is required',
            'name.unique' => 'A permission with this name already exists',
            'name.max' => 'Permission name must not exceed 255 characters',
            'category.required' => 'Permission category is required',
            'category.max' => 'Category must not exceed 100 characters',
            'description.max' => 'Description must not exceed 1000 characters',
            'name_ar.max' => 'Arabic name must not exceed 255 characters',
        ];
    }
}
