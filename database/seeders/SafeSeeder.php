<?php

namespace Database\Seeders;

use App\Models\Safe;
use App\Models\SafeTransaction;
use Illuminate\Database\Seeder;

class SafeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $safes = [
            [
                'name' => 'Main Office Safe',
                'location' => 'Riyadh HQ - Finance Department',
                'balance' => 50000.00,
                'max_balance' => 100000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Cash Register Safe',
                'location' => 'Sales Counter - Riyadh',
                'balance' => 15000.00,
                'max_balance' => 30000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Jeddah Branch Safe',
                'location' => 'Jeddah Office - Reception',
                'balance' => 25000.00,
                'max_balance' => 50000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Emergency Reserve Fund',
                'location' => 'Headquarters - Secured Room',
                'balance' => 75000.00,
                'max_balance' => 150000.00,
                'is_active' => true,
            ],
        ];

        $createdSafes = [];
        foreach ($safes as $safe) {
            $createdSafes[] = Safe::create($safe);
        }

        // Add sample transactions
        $transactions = [
            // Main Office Safe
            [
                'safe_id' => $createdSafes[0]->id,
                'type' => 'deposit',
                'amount' => 10000.00,
                'reference_type' => 'bank_transfer',
                'reference_id' => 1,
                'user_id' => 1,
                'description' => 'Daily bank deposit - Sales',
                'created_at' => now()->subDays(5),
            ],
            [
                'safe_id' => $createdSafes[0]->id,
                'type' => 'withdrawal',
                'amount' => 5000.00,
                'reference_type' => 'expense',
                'reference_id' => 1,
                'user_id' => 1,
                'description' => 'Office supplies payment',
                'created_at' => now()->subDays(4),
            ],
            [
                'safe_id' => $createdSafes[0]->id,
                'type' => 'deposit',
                'amount' => 8000.00,
                'reference_type' => 'cash_register',
                'reference_id' => 1,
                'user_id' => 1,
                'description' => 'Daily cash collection',
                'created_at' => now()->subDays(3),
            ],

            // Cash Register Safe
            [
                'safe_id' => $createdSafes[1]->id,
                'type' => 'deposit',
                'amount' => 3000.00,
                'reference_type' => 'cash_register',
                'reference_id' => 2,
                'user_id' => 1,
                'description' => 'Morning cash count',
                'created_at' => now()->subDays(2),
            ],
            [
                'safe_id' => $createdSafes[1]->id,
                'type' => 'withdrawal',
                'amount' => 2000.00,
                'reference_type' => 'expense',
                'reference_id' => 2,
                'user_id' => 1,
                'description' => 'Petty cash for supplies',
                'created_at' => now()->subDays(1),
            ],
            [
                'safe_id' => $createdSafes[1]->id,
                'type' => 'deposit',
                'amount' => 1500.00,
                'reference_type' => 'cash_register',
                'reference_id' => 3,
                'user_id' => 1,
                'description' => 'Evening cash deposit',
                'created_at' => now(),
            ],

            // Jeddah Branch Safe
            [
                'safe_id' => $createdSafes[2]->id,
                'type' => 'deposit',
                'amount' => 12000.00,
                'reference_type' => 'bank_transfer',
                'reference_id' => 2,
                'user_id' => 1,
                'description' => 'Jeddah branch opening balance',
                'created_at' => now()->subDays(10),
            ],
            [
                'safe_id' => $createdSafes[2]->id,
                'type' => 'withdrawal',
                'amount' => 3000.00,
                'reference_type' => 'expense',
                'reference_id' => 3,
                'user_id' => 1,
                'description' => 'Branch operational expenses',
                'created_at' => now()->subDays(7),
            ],

            // Emergency Reserve Fund
            [
                'safe_id' => $createdSafes[3]->id,
                'type' => 'deposit',
                'amount' => 75000.00,
                'reference_type' => 'bank_transfer',
                'reference_id' => 3,
                'user_id' => 1,
                'description' => 'Emergency fund establishment',
                'created_at' => now()->subDays(30),
            ],
        ];

        foreach ($transactions as $transaction) {
            SafeTransaction::create($transaction);
        }
    }
}
