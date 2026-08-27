<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreStorageRequest - Validation for creating storage/warehouses
 */
class StoreStorageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled in controller
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:storages,name',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'capacity' => 'nullable|numeric|min:0|max:999999.99',
            'storage_type' => 'required|string|max:50|in:warehouse,shelf,cabinet,room',
            'is_active' => 'nullable|boolean',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:branches,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Storage name is required',
            'name.unique' => 'A storage with this name already exists',
            'location.required' => 'Storage location is required',
            'storage_type.required' => 'Storage type is required',
            'storage_type.in' => 'Storage type must be one of: warehouse, shelf, cabinet, room',
            'capacity.numeric' => 'Capacity must be a valid number',
            'capacity.min' => 'Capacity must be greater than or equal to 0',
        ];
    }
}
