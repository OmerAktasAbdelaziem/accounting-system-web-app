<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Display a listing of all permissions
     */
    public function index()
    {
        $permissions = Permission::with('roles')
            ->orderBy('category')
            ->orderBy('name')
            ->paginate(50);

        return view('permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new permission
     */
    public function create()
    {
        $categories = Permission::distinct()->pluck('category')->filter()->sort();
        return view('permissions.create', compact('categories'));
    }

    /**
     * Store a newly created permission in storage
     */
    public function store(StorePermissionRequest $request)
    {
        $validated = $request->validated();

        Permission::create($validated);

        return redirect()->route('permissions.index')
            ->with('success', __('messages.permission_created_successfully'));
    }

    /**
     * Show the form for editing the specified permission
     */
    public function edit(Permission $permission)
    {
        $categories = Permission::distinct()->pluck('category')->filter()->sort();
        return view('permissions.edit', compact('permission', 'categories'));
    }

    /**
     * Update the specified permission in storage
     */
    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        $validated = $request->validated();

        $permission->update($validated);

        return redirect()->route('permissions.index')
            ->with('success', __('messages.permission_updated_successfully'));
    }

    /**
     * Remove the specified permission from storage
     */
    public function destroy(Permission $permission)
    {
        // Check if permission is assigned to roles
        if ($permission->roles()->exists()) {
            return redirect()->back()
                ->with('error', __('messages.cannot_delete_permission_in_use'));
        }

        $permission->delete();

        return redirect()->route('permissions.index')
            ->with('success', __('messages.permission_deleted_successfully'));
    }
}
