<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Merchant;
use App\Models\VatRate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VatRateController extends Controller
{
    /**
     * Display a listing of the VAT rates.
     */
    public function index()
    {
        $merchants = Merchant::with('vatRates')->get();
        return view('super-admin.vat-rates.index', compact('merchants'));
    }

    /**
     * Store a newly created VAT rate in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
            'rate_percentage' => 'required|numeric|min:0|max:100',
            'applies_to' => 'required|in:invoices,all',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Check if merchant already has a VAT rate
        $existing = VatRate::where('merchant_id', $validated['merchant_id'])->first();
        if ($existing) {
            return redirect()->back()->with('error', 'This merchant already has a VAT rate configured.');
        }

        VatRate::create($validated);

        return redirect()->route('super-admin.vat-rates.index')
            ->with('success', 'VAT rate created successfully.');
    }

    /**
     * Update the specified VAT rate in storage.
     */
    public function update(Request $request, VatRate $vat_rate)
    {
        $validated = $request->validate([
            'rate_percentage' => 'required|numeric|min:0|max:100',
            'applies_to' => 'required|in:invoices,all',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $vat_rate->update($validated);

        return redirect()->route('super-admin.vat-rates.index')
            ->with('success', 'VAT rate updated successfully.');
    }

    /**
     * Remove the specified VAT rate from storage.
     */
    public function destroy(VatRate $vat_rate)
    {
        $merchant = $vat_rate->merchant;
        $vat_rate->delete();

        return redirect()->route('super-admin.vat-rates.index')
            ->with('success', "VAT rate for {$merchant->name} has been deleted.");
    }
}
