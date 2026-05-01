<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Employees (الموظفون)
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code')->unique(); // e.g., EMP-001
            $table->string('name');
            $table->string('name_ar'); // Arabic name
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('position');
            $table->string('position_ar'); // Arabic position
            $table->text('address')->nullable();
            $table->text('address_ar')->nullable(); // Arabic address
            $table->date('hire_date');
            $table->date('termination_date')->nullable(); // NULL if still employed
            $table->decimal('base_salary', 10, 2); // Base monthly salary
            $table->decimal('commission_rate', 5, 2)->default(0); // Commission percentage (5, 2 allows 0-999.99%)
            $table->enum('commission_type', ['percentage', 'fixed'])->default('percentage'); // Percentage or fixed amount
            $table->enum('department', ['sales', 'inventory', 'accounting', 'management', 'other'])->default('sales');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('employee_code');
            $table->index('email');
            $table->index('department');
            $table->index('is_active');
        });

        // Employee Commissions (عمولات الموظفين)
        Schema::create('employee_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->integer('month'); // 1-12
            $table->integer('year'); // e.g., 2024
            $table->decimal('sales_amount', 12, 2)->default(0); // Total sales for the month
            $table->integer('sales_count')->default(0); // Number of transactions
            $table->decimal('commission_earned', 10, 2)->default(0); // Calculated commission
            $table->decimal('bonus', 10, 2)->default(0); // Additional bonus if any
            $table->string('status')->default('pending'); // pending, approved, paid
            $table->text('notes')->nullable();
            $table->text('notes_ar')->nullable(); // Arabic notes
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['employee_id', 'month', 'year']);
            $table->index('employee_id');
            $table->index('status');
            $table->index(['month', 'year']);
        });

        // Employee Deductions (خصومات الموظفين)
        Schema::create('employee_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->integer('month'); // 1-12
            $table->integer('year'); // e.g., 2024
            $table->string('type'); // e.g., 'tax', 'insurance', 'loan', 'other'
            $table->string('type_ar'); // Arabic type
            $table->decimal('amount', 10, 2);
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable(); // Arabic description
            $table->string('status')->default('pending'); // pending, approved, deducted
            $table->dateTime('deducted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('employee_id');
            $table->index('type');
            $table->index('status');
            $table->index(['month', 'year']);
        });

        // Employee Sales (مبيعات الموظفين)
        Schema::create('employee_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_amount', 12, 2); // quantity * unit_price
            $table->date('sale_date');
            $table->string('sale_reference')->nullable(); // Reference to invoice/receipt
            $table->text('notes')->nullable();
            $table->text('notes_ar')->nullable(); // Arabic notes
            $table->timestamps();
            $table->softDeletes();

            $table->index('employee_id');
            $table->index('product_id');
            $table->index('sale_date');
            $table->index(['employee_id', 'sale_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_sales');
        Schema::dropIfExists('employee_deductions');
        Schema::dropIfExists('employee_commissions');
        Schema::dropIfExists('employees');
    }
};
