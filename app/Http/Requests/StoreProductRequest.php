<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreProductRequest - Validation for creating products
 */
class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled in controller
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:products,name',
            'name_ar' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'required|string|max:50|unique:products,sku',
            'description' => 'nullable|string|max:1000',
            'description_ar' => 'nullable|string|max:1000',
            'unit_price' => 'required|numeric|min:0.01|max:999999.99',
            'reorder_level' => 'required|integer|min:1|max:10000',
            'status' => 'nullable|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required',
            'name.unique' => 'A product with this name already exists',
            'category_id.required' => 'Product category is required',
            'category_id.exists' => 'Selected category does not exist',
            'sku.required' => 'SKU (product code) is required',
            'sku.unique' => 'A product with this SKU already exists',
            'unit_price.required' => 'Unit price is required',
            'unit_price.numeric' => 'Unit price must be a valid number',
            'unit_price.min' => 'Unit price must be greater than 0',
            'reorder_level.required' => 'Reorder level is required',
            'reorder_level.integer' => 'Reorder level must be a whole number',
        ];
    }
}
