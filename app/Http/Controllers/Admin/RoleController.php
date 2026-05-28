<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\BranchAccess;
use App\Models\Branch;
use App\Models\Merchant;
use App\Models\Role;
use App\Models\Permission;
use App\Models\FeatureAccess;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (! $user || ! method_exists($user, 'isSuperAdmin') || ! $user->isSuperAdmin()) {
                abort(403);
            }
            return $next($request);
        });
    }
    /**
     * Display a listing of all roles
     */
    public function index()
    {
        $roles = Role::with(['permissions', 'branchAccesses'])
            ->withCount('users')
            ->orderBy('created_at', 'desc')
            ->paginate(25);
        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role
     */
    public function create()
    {
        $permissions = Permission::orderBy('name')->get();
        $merchants = Merchant::with(['branches' => fn ($query) => $query->orderBy('name')])
            ->orderBy('name')
            ->get();

        $availableFeatures = \App\Http\Controllers\SuperAdmin\FeatureAccessController::getAvailableFeatures();

        return view('roles.create', compact('permissions', 'merchants', 'availableFeatures'));
    }

    /**
     * Store a newly created role in storage
     */
    public function store(StoreRoleRequest $request)
    {
        $validated = $request->validated();

        $role = Role::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        if (!empty($validated['permissions'])) {
            $role->permissions()->attach($validated['permissions']);
        }

        BranchAccess::syncForRole($role, $validated['branch_ids'] ?? []);

        // Persist feature toggles for this role across all merchants if provided
        if (!empty($validated['features']) && is_array($validated['features'])) {
            $featureKeys = array_values($validated['features']);
            $merchants = Merchant::all();
            foreach ($merchants as $merchant) {
                // disable all first
                FeatureAccess::where('merchant_id', $merchant->id)->where('role_id', $role->id)->update(['is_enabled' => false]);
                foreach ($featureKeys as $featureKey) {
                    FeatureAccess::updateOrCreate([
                        'merchant_id' => $merchant->id,
                        'role_id' => $role->id,
                        'feature_key' => $featureKey,
                    ], ['is_enabled' => true]);
                }
            }
        }

        return redirect()->route('roles.index')
            ->with('success', __('messages.role_created_successfully'));
    }

    /**
     * Show the form for editing the specified role
     */
    public function edit(Role $role)
    {
        if (in_array($role->name, ['Admin', 'System'])) {
            return redirect()->back()
                ->with('error', __('messages.cannot_edit_system_roles'));
        }

        $role->load(['permissions', 'branchAccesses.branch', 'branchAccesses.merchant']);
        $permissions = Permission::orderBy('name')->get();
        $selectedPermissions = $role->permissions->pluck('id')->toArray();
        $selectedBranchIds = $role->branchAccesses->pluck('branch_id')->map(fn ($branchId) => (int) $branchId)->all();
        $merchants = Merchant::with(['branches' => fn ($query) => $query->orderBy('name')])
            ->orderBy('name')
            ->get();

        $availableFeatures = \App\Http\Controllers\SuperAdmin\FeatureAccessController::getAvailableFeatures();

        $selectedFeatures = FeatureAccess::where('role_id', $role->id)->where('is_enabled', true)->pluck('feature_key')->unique()->values()->all();

        return view('roles.edit', compact('role', 'permissions', 'selectedPermissions', 'selectedBranchIds', 'merchants', 'availableFeatures', 'selectedFeatures'));
    }

    /**
     * Update the specified role in storage
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        if (in_array($role->name, ['Admin', 'System'])) {
            return redirect()->back()
                ->with('error', __('messages.cannot_edit_system_roles'));
        }

        $validated = $request->validated();

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        // Sync permissions
        if (!empty($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        } else {
            $role->permissions()->detach();
        }

        BranchAccess::syncForRole($role, $validated['branch_ids'] ?? []);

        // Sync feature toggles across all merchants
        $availableFeatures = \App\Http\Controllers\SuperAdmin\FeatureAccessController::getAvailableFeatures();
        $selectedFeatures = $validated['features'] ?? [];

        $merchants = Merchant::all();
        foreach ($merchants as $merchant) {
            // disable all for this role/merchant
            FeatureAccess::where('merchant_id', $merchant->id)->where('role_id', $role->id)->update(['is_enabled' => false]);

            // enable selected ones
            foreach ($selectedFeatures as $featureKey) {
                FeatureAccess::updateOrCreate([
                    'merchant_id' => $merchant->id,
                    'role_id' => $role->id,
                    'feature_key' => $featureKey,
                ], ['is_enabled' => true]);
            }
        }

        return redirect()->route('roles.index')
            ->with('success', __('messages.role_updated_successfully'));
    }

    /**
     * Remove the specified role from storage
     */
    public function destroy(Role $role)
    {
        // Prevent deleting system roles
        if (in_array($role->name, ['Admin', 'System'])) {
            return redirect()->back()
                ->with('error', __('messages.cannot_delete_system_roles'));
        }

        // Check if role is assigned to users
        if ($role->users()->exists()) {
            return redirect()->back()
                ->with('error', __('messages.cannot_delete_role_with_users'));
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', __('messages.role_deleted_successfully'));
    }
}
