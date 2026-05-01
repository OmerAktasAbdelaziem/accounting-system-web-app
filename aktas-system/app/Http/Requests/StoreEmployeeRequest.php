<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreEmployeeRequest - Validation for creating employees
 */
class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_code' => 'required|string|max:20|unique:employees,employee_code',
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:employees,email',
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:100',
            'position_ar' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'address_ar' => 'nullable|string|max:500',
            'hire_date' => 'required|date',
            'termination_date' => 'nullable|date|after:hire_date',
            'base_salary' => 'required|numeric|min:0|max:999999.99',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'commission_type' => 'required|in:percentage,fixed',
            'department' => 'required|in:sales,inventory,accounting,management,other',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'employee_code.required' => 'Employee code is required',
            'employee_code.unique' => 'This employee code already exists',
            'name.required' => 'Employee name is required',
            'email.unique' => 'This email is already registered',
            'hire_date.required' => 'Hire date is required',
            'hire_date.date' => 'Hire date must be a valid date',
            'termination_date.after' => 'Termination date must be after hire date',
            'base_salary.required' => 'Base salary is required',
            'base_salary.numeric' => 'Base salary must be a number',
            'commission_rate.required' => 'Commission rate is required',
            'commission_type.required' => 'Commission type must be specified',
            'department.required' => 'Department is required',
        ];
    }
}
