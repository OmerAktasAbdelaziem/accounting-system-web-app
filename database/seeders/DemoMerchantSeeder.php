<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\Merchant;
use App\Models\Package;
use App\Models\Subscription;
use App\Models\User;
use App\Models\VatRate;
use Illuminate\Database\Seeder;

class DemoMerchantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get packages
        $basicPackage = Package::where('name', 'Basic')->first();
        $professionalPackage = Package::where('name', 'Professional')->first();
        $enterprisePackage = Package::where('name', 'Enterprise')->first();

        // Get currencies
        $usd = Currency::where('code', 'USD')->first();
        $eur = Currency::where('code', 'EUR')->first();
        $egp = Currency::where('code', 'EGP')->first();
        $try = Currency::where('code', 'TRY')->first();

        // Demo Merchant 1: Basic Plan (USD)
        $merchant1 = Merchant::create([
            'name' => 'ABC Corporation',
            'admin_email' => 'admin@abccorp.com',
            'phone' => '+1-555-0101',
            'address' => '123 Business St, New York, NY',
            'default_currency_id' => $usd->id,
            'max_employees' => $basicPackage->max_employees,
            'max_currencies' => $basicPackage->max_currencies,
            'max_languages' => $basicPackage->max_languages,
            'is_active' => true,
        ]);

        // Create admin user for merchant 1
        $user1 = User::create([
            'name' => 'John Admin',
            'email' => 'admin@abccorp.com',
            'password' => bcrypt('password123'),
            'user_type' => 'merchant_admin',
            'merchant_id' => $merchant1->id,
            'default_currency_id' => $usd->id,
            'default_language' => 'en',
        ]);

        // Add currencies to merchant 1
        $merchant1->currencies()->attach($usd->id, ['is_default' => true]);

        // Create subscription for merchant 1
        Subscription::create([
            'merchant_id' => $merchant1->id,
            'package_id' => $basicPackage->id,
            'start_date' => now(),
            'expires_at' => now()->addDays($basicPackage->duration_days),
            'is_active' => true,
        ]);

        // Set VAT rate for merchant 1
        VatRate::create([
            'merchant_id' => $merchant1->id,
            'rate_percentage' => 10,
            'is_active' => true,
            'applies_to' => 'invoices',
        ]);

        // Demo Merchant 2: Professional Plan (EUR)
        $merchant2 = Merchant::create([
            'name' => 'Tech Solutions GmbH',
            'admin_email' => 'admin@techsolutions.de',
            'phone' => '+49-30-55501202',
            'address' => '456 Innovation Ave, Berlin, Germany',
            'default_currency_id' => $eur->id,
            'max_employees' => $professionalPackage->max_employees,
            'max_currencies' => $professionalPackage->max_currencies,
            'max_languages' => $professionalPackage->max_languages,
            'is_active' => true,
        ]);

        // Create admin user for merchant 2
        $user2 = User::create([
            'name' => 'Petra Manager',
            'email' => 'admin@techsolutions.de',
            'password' => bcrypt('password123'),
            'user_type' => 'merchant_admin',
            'merchant_id' => $merchant2->id,
            'default_currency_id' => $eur->id,
            'default_language' => 'de',
        ]);

        // Add currencies to merchant 2 (multi-currency)
        $merchant2->currencies()->attach($eur->id, ['is_default' => true]);
        $merchant2->currencies()->attach($usd->id, ['is_default' => false]);

        // Create subscription for merchant 2
        Subscription::create([
            'merchant_id' => $merchant2->id,
            'package_id' => $professionalPackage->id,
            'start_date' => now(),
            'expires_at' => now()->addDays($professionalPackage->duration_days),
            'is_active' => true,
        ]);

        // Set VAT rate for merchant 2
        VatRate::create([
            'merchant_id' => $merchant2->id,
            'rate_percentage' => 19,
            'is_active' => true,
            'applies_to' => 'all',
        ]);

        // Create employees for merchant 2
        User::create([
            'name' => 'Anna Employee',
            'email' => 'anna@techsolutions.de',
            'password' => bcrypt('password123'),
            'user_type' => 'employee',
            'merchant_id' => $merchant2->id,
            'default_currency_id' => $eur->id,
            'default_language' => 'de',
        ]);

        // Demo Merchant 3: Enterprise Plan (Multi-currency - EGP + TRY)
        $merchant3 = Merchant::create([
            'name' => 'Global Trade Inc',
            'admin_email' => 'admin@globaltrade.eg',
            'phone' => '+20-2-24123456',
            'address' => '789 Commerce Plaza, Cairo, Egypt',
            'default_currency_id' => $egp->id,
            'max_employees' => $enterprisePackage->max_employees,
            'max_currencies' => $enterprisePackage->max_currencies,
            'max_languages' => $enterprisePackage->max_languages,
            'is_active' => true,
        ]);

        // Create admin user for merchant 3
        $user3 = User::create([
            'name' => 'Ahmed Manager',
            'email' => 'admin@globaltrade.eg',
            'password' => bcrypt('password123'),
            'user_type' => 'merchant_admin',
            'merchant_id' => $merchant3->id,
            'default_currency_id' => $egp->id,
            'default_language' => 'ar',
        ]);

        // Add multiple currencies to merchant 3
        $merchant3->currencies()->attach($egp->id, ['is_default' => true]);
        $merchant3->currencies()->attach($try->id, ['is_default' => false]);
        $merchant3->currencies()->attach($usd->id, ['is_default' => false]);

        // Create subscription for merchant 3
        Subscription::create([
            'merchant_id' => $merchant3->id,
            'package_id' => $enterprisePackage->id,
            'start_date' => now(),
            'expires_at' => now()->addDays($enterprisePackage->duration_days),
            'is_active' => true,
        ]);

        // Set VAT rate for merchant 3
        VatRate::create([
            'merchant_id' => $merchant3->id,
            'rate_percentage' => 14,
            'is_active' => true,
            'applies_to' => 'invoices',
        ]);

        // Create multiple employees for merchant 3
        User::create([
            'name' => 'Fatima Employee',
            'email' => 'fatima@globaltrade.eg',
            'password' => bcrypt('password123'),
            'user_type' => 'employee',
            'merchant_id' => $merchant3->id,
            'default_currency_id' => $egp->id,
            'default_language' => 'ar',
        ]);

        User::create([
            'name' => 'Mahmoud Employee',
            'email' => 'mahmoud@globaltrade.eg',
            'password' => bcrypt('password123'),
            'user_type' => 'employee',
            'merchant_id' => $merchant3->id,
            'default_currency_id' => $egp->id,
            'default_language' => 'ar',
        ]);

        $this->command->info('Demo merchants seeded successfully!');
        $this->command->line('Merchant 1: ' . $merchant1->name . ' (Basic Plan, USD)');
        $this->command->line('Merchant 2: ' . $merchant2->name . ' (Professional Plan, EUR)');
        $this->command->line('Merchant 3: ' . $merchant3->name . ' (Enterprise Plan, Multi-currency)');
    }
}
