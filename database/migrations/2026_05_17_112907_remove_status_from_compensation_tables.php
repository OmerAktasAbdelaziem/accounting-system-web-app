<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('employee_advances') && Schema::hasColumn('employee_advances', 'status')) {
            if (DB::connection()->getDriverName() === 'sqlite') {
                DB::statement('PRAGMA foreign_keys=OFF');

                DB::statement("CREATE TABLE employee_advances_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    employee_id INTEGER NOT NULL,
                    amount NUMERIC NOT NULL,
                    advance_date DATE NOT NULL,
                    description VARCHAR(255) NULL,
                    created_by INTEGER NULL,
                    created_at DATETIME NULL,
                    updated_at DATETIME NULL,
                    deleted_at DATETIME NULL
                )");

                DB::statement("INSERT INTO employee_advances_new (id, employee_id, amount, advance_date, description, created_by, created_at, updated_at, deleted_at)
                    SELECT id, employee_id, amount, advance_date, description, created_by, created_at, updated_at, deleted_at
                    FROM employee_advances");

                DB::statement('DROP TABLE employee_advances');
                DB::statement('ALTER TABLE employee_advances_new RENAME TO employee_advances');
                DB::statement('CREATE INDEX employee_advances_employee_id_index ON employee_advances (employee_id)');
                DB::statement('PRAGMA foreign_keys=ON');
            } else {
                Schema::table('employee_advances', function (Blueprint $table) {
                    $table->dropColumn('status');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('employee_advances') && !Schema::hasColumn('employee_advances', 'status')) {
            if (DB::connection()->getDriverName() === 'sqlite') {
                DB::statement('PRAGMA foreign_keys=OFF');

                DB::statement("CREATE TABLE employee_advances_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    employee_id INTEGER NOT NULL,
                    amount NUMERIC NOT NULL,
                    advance_date DATE NOT NULL,
                    description VARCHAR(255) NULL,
                    status VARCHAR(255) NOT NULL DEFAULT 'pending',
                    created_by INTEGER NULL,
                    created_at DATETIME NULL,
                    updated_at DATETIME NULL,
                    deleted_at DATETIME NULL
                )");

                DB::statement("INSERT INTO employee_advances_new (id, employee_id, amount, advance_date, description, status, created_by, created_at, updated_at, deleted_at)
                    SELECT id, employee_id, amount, advance_date, description, 'pending', created_by, created_at, updated_at, deleted_at
                    FROM employee_advances");

                DB::statement('DROP TABLE employee_advances');
                DB::statement('ALTER TABLE employee_advances_new RENAME TO employee_advances');
                DB::statement('CREATE INDEX employee_advances_employee_id_index ON employee_advances (employee_id)');
                DB::statement('CREATE INDEX employee_advances_status_index ON employee_advances (status)');
                DB::statement('PRAGMA foreign_keys=ON');
            } else {
                Schema::table('employee_advances', function (Blueprint $table) {
                    $table->string('status')->default('pending');
                    $table->index('status');
                });
            }
        }
    }
};
