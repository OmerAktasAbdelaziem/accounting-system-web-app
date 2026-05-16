<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreCommissionRequest - Validation for creating commissions
 */
class StoreCommissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled in controller
    }

    public function rules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'sale_amount' => 'required|numeric|min:0|max:999999.99',
            'commission_date' => 'required|date|before_or_equal:today',
            'reference_type' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.required' => 'Employee is required',
            'employee_id.exists' => 'Selected employee does not exist',
            'commission_rate.required' => 'Commission rate is required',
            'commission_rate.numeric' => 'Commission rate must be a valid number',
            'commission_rate.min' => 'Commission rate must be greater than or equal to 0',
            'commission_rate.max' => 'Commission rate must not exceed 100%',
            'sale_amount.required' => 'Sale amount is required',
            'sale_amount.numeric' => 'Sale amount must be a valid number',
            'sale_amount.min' => 'Sale amount must be greater than or equal to 0',
            'commission_date.required' => 'Commission date is required',
            'commission_date.date' => 'Commission date must be a valid date',
            'commission_date.before_or_equal' => 'Commission date cannot be in the future',
        ];
    }
}
