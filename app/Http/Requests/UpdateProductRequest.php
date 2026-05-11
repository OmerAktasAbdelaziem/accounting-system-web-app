<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateProductRequest - Validation for updating products
 */
class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product');

        return [
            'name' => 'sometimes|required|string|max:255|unique:products,name,' . $productId,
            'name_ar' => 'nullable|string|max:255',
            'category_id' => 'sometimes|required|exists:categories,id',
            'sku' => 'sometimes|required|string|max:50|unique:products,sku,' . $productId,
            'description' => 'nullable|string|max:1000',
            'description_ar' => 'nullable|string|max:1000',
            'unit_price' => 'sometimes|required|numeric|min:0.01|max:999999.99',
            'reorder_level' => 'sometimes|required|integer|min:1|max:10000',
            'status' => 'nullable|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required',
            'name.unique' => 'A product with this name already exists',
            'unit_price.min' => 'Unit price must be greater than 0',
            'reorder_level.integer' => 'Reorder level must be a whole number',
        ];
    }
}
