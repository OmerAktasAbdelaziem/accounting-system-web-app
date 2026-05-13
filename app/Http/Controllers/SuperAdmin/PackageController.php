<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageFeature;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    /**
     * Display all packages
     */
    public function index()
    {
        $this->authorize('viewAny', Package::class);

        $packages = Package::with('features')->latest()->paginate(15);

        return view('super-admin.packages.index', compact('packages'));
    }

    /**
     * Show package creation form
     */
    public function create()
    {
        $this->authorize('create', Package::class);

        $allFeatures = $this->getAllAvailableFeatures();

        return view('super-admin.packages.create', compact('allFeatures'));
    }

    /**
     * Store new package
     */
    public function store(Request $request)
    {
        $this->authorize('create', Package::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:packages',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'max_employees' => 'nullable|integer|min:1',
            'max_currencies' => 'required|integer|min:1|max:20',
            'max_languages' => 'required|integer|min:1|max:10',
            'is_active' => 'boolean',
            'features' => 'nullable|array',
            'features.*' => 'string|in:' . implode(',', array_keys($this->getAllAvailableFeatures())),
        ]);

        $package = Package::create($validated);

        // Add features to package
        if (isset($validated['features'])) {
            $features = $this->getAllAvailableFeatures();
            foreach ($validated['features'] as $featureKey) {
                PackageFeature::create([
                    'package_id' => $package->id,
                    'feature_key' => $featureKey,
                    'feature_name' => $features[$featureKey],
                ]);
            }
        }

        return redirect()->route('super-admin.packages.show', $package)
            ->with('success', 'Package created successfully');
    }

    /**
     * Show package details
     */
    public function show(Package $package)
    {
        $this->authorize('view', $package);

        $package->load('features');

        return view('super-admin.packages.show', compact('package'));
    }

    /**
     * Show package edit form
     */
    public function edit(Package $package)
    {
        $this->authorize('update', $package);

        $package->load('features');
        $allFeatures = $this->getAllAvailableFeatures();
        $selectedFeatures = $package->features->pluck('feature_key')->toArray();

        return view('super-admin.packages.edit', compact('package', 'allFeatures', 'selectedFeatures'));
    }

    /**
     * Update package
     */
    public function update(Request $request, Package $package)
    {
        $this->authorize('update', $package);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:packages,name,' . $package->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'max_employees' => 'nullable|integer|min:1',
            'max_currencies' => 'required|integer|min:1|max:20',
            'max_languages' => 'required|integer|min:1|max:10',
            'is_active' => 'boolean',
            'features' => 'nullable|array',
            'features.*' => 'string|in:' . implode(',', array_keys($this->getAllAvailableFeatures())),
        ]);

        $package->update($validated);

        // Update features
        $package->features()->delete();
        if (isset($validated['features'])) {
            $features = $this->getAllAvailableFeatures();
            foreach ($validated['features'] as $featureKey) {
                PackageFeature::create([
                    'package_id' => $package->id,
                    'feature_key' => $featureKey,
                    'feature_name' => $features[$featureKey],
                ]);
            }
        }

        return redirect()->route('super-admin.packages.show', $package)
            ->with('success', 'Package updated successfully');
    }

    /**
     * Delete package
     */
    public function destroy(Package $package)
    {
        $this->authorize('delete', $package);

        // Check if package is in use
        if ($package->subscriptions()->exists()) {
            return redirect()->back()->withErrors(['error' => 'Cannot delete package with active subscriptions']);
        }

        $package->delete();

        return redirect()->route('super-admin.packages.index')
            ->with('success', 'Package deleted successfully');
    }

    /**
     * Get all available features
     */
    private function getAllAvailableFeatures(): array
    {
        return [
            'invoicing' => 'Invoicing & Billing',
            'payroll' => 'Payroll Management',
            'inventory' => 'Inventory Management',
            'basic_reporting' => 'Basic Reporting',
            'advanced_reporting' => 'Advanced Reporting',
            'multi_branch' => 'Multi-Branch Support',
            'api_access' => 'API Access',
            'custom_integration' => 'Custom Integration',
            'dedicated_support' => 'Dedicated Support',
            'backup_restore' => 'Backup & Restore',
            'audit_logs' => 'Audit Logs',
            'user_management' => 'User Management',
        ];
    }
}
