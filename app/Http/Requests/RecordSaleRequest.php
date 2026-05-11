<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * RecordSaleRequest - Validation for recording employee sales
 */
class RecordSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:100000',
            'unit_price' => 'required|numeric|min:0.01|max:999999.99',
            'sale_date' => 'required|date|before_or_equal:today',
            'sale_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
            'notes_ar' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Product is required',
            'product_id.exists' => 'Selected product does not exist',
            'quantity.required' => 'Quantity is required',
            'quantity.integer' => 'Quantity must be a whole number',
            'quantity.min' => 'Quantity must be at least 1',
            'unit_price.required' => 'Unit price is required',
            'unit_price.numeric' => 'Unit price must be a valid number',
            'sale_date.required' => 'Sale date is required',
            'sale_date.date' => 'Sale date must be a valid date',
            'sale_date.before_or_equal' => 'Sale date cannot be in the future',
        ];
    }

    /**
     * Get custom attributes for validator errors
     */
    public function attributes(): array
    {
        return [
            'product_id' => 'product',
            'quantity' => 'quantity',
            'unit_price' => 'unit price',
            'sale_date' => 'sale date',
        ];
    }
}
