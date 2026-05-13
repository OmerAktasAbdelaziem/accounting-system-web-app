<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->string('feature_key'); // 'invoicing', 'payroll', 'inventory', etc
            $table->string('feature_name');
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->unique(['package_id', 'feature_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_features');
    }
};
