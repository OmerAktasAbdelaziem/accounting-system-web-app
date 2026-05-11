<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateEmployeeRequest - Validation for updating employees
 */
class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employeeId = $this->route('employee');

        return [
            'employee_code' => 'sometimes|required|string|max:20|unique:employees,employee_code,' . $employeeId,
            'name' => 'sometimes|required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:employees,email,' . $employeeId,
            'phone' => 'nullable|string|max:20',
            'position' => 'sometimes|required|string|max:100',
            'position_ar' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'address_ar' => 'nullable|string|max:500',
            'hire_date' => 'sometimes|required|date',
            'termination_date' => 'nullable|date|after:hire_date',
            'base_salary' => 'sometimes|required|numeric|min:0|max:999999.99',
            'commission_rate' => 'sometimes|required|numeric|min:0|max:100',
            'commission_type' => 'sometimes|required|in:percentage,fixed',
            'department' => 'sometimes|required|in:sales,inventory,accounting,management,other',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'employee_code.unique' => 'This employee code already exists',
            'email.unique' => 'This email is already registered',
            'termination_date.after' => 'Termination date must be after hire date',
            'base_salary.numeric' => 'Base salary must be a number',
            'commission_type.in' => 'Commission type must be either percentage or fixed',
        ];
    }
}
