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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->constrained('roles')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->text('notes')->nullable();
            $table->dateTime('last_login')->nullable();
        });

        // Create audit log table
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action'); // created, updated, deleted, viewed, etc.
            $table->string('action_ar')->nullable();
            $table->string('model_type'); // User, Product, Invoice, etc.
            $table->unsignedBigInteger('model_id');
            $table->text('changes')->nullable(); // JSON format: {old: {...}, new: {...}}
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['role_id']);
            $table->dropColumn(['role_id', 'is_active', 'phone', 'address', 'notes', 'last_login']);
        });
    }
};
