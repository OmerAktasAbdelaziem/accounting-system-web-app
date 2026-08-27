<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branchables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('branchable_type');
            $table->unsignedBigInteger('branchable_id');
            $table->timestamps();

            $table->unique(['branch_id', 'branchable_type', 'branchable_id'], 'branchables_unique');
            $table->index(['branchable_type', 'branchable_id'], 'branchables_type_id_index');
        });

        $map = [
            'employees' => \App\Models\Employee::class,
            'products' => \App\Models\Product::class,
            'storages' => \App\Models\Storage::class,
            'safes' => \App\Models\Safe::class,
            'journal_entries' => \App\Models\JournalEntry::class,
            'invoices' => \App\Models\Invoice::class,
        ];

        foreach ($map as $table => $type) {
            if (!Schema::hasColumn($table, 'branch_id')) {
                continue;
            }

            DB::table($table)
                ->select('id', 'branch_id')
                ->whereNotNull('branch_id')
                ->orderBy('id')
                ->chunkById(500, function ($rows) use ($type) {
                    foreach ($rows as $row) {
                        DB::table('branchables')->insertOrIgnore([
                            'branch_id' => $row->branch_id,
                            'branchable_type' => $type,
                            'branchable_id' => $row->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('branchables');
    }
};