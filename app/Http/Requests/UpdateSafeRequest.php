<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateSafeRequest - Validation for updating safes
 */
class UpdateSafeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled in controller
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:safes,name,' . $this->safe->id,
            'location' => 'required|string|max:255',
            'max_balance' => 'nullable|numeric|min:0|max:999999.99',
            'is_active' => 'nullable|boolean',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:branches,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Safe name is required',
            'name.unique' => 'A safe with this name already exists',
            'location.required' => 'Safe location is required',
            'max_balance.numeric' => 'Max balance must be a valid number',
            'max_balance.min' => 'Max balance must be greater than or equal to 0',
        ];
    }
}
