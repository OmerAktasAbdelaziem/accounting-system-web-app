<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\ResetUserPasswordRequest;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of all users
     */
    public function index(Request $request)
    {
        $query = User::with('role')
            ->orderBy('created_at', 'desc');

        // Filter by role
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active == '1');
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(25);
        $roles = Role::all();

        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);

        User::create($validated);

        return redirect()->route('users.index')
            ->with('success', __('messages.user_created_successfully'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active', $user->is_active);

        $user->update($validated);

        return redirect()->route('users.index')
            ->with('success', __('messages.user_updated_successfully'));
    }

    /**
     * Remove the specified user from storage
     */
    public function destroy(User $user)
    {
        // Prevent deleting the currently logged-in user
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', __('messages.cannot_delete_own_account'));
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', __('messages.user_deleted_successfully'));
    }

    /**
     * Activate or deactivate a user
     */
    public function toggleStatus(User $user)
    {
        // Prevent disabling the currently logged-in user
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', __('messages.cannot_modify_own_account'));
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';
        return redirect()->back()
            ->with('success', __('messages.user_' . $status));
    }

    /**
     * Reset user password
     */
    public function resetPassword(ResetUserPasswordRequest $request, User $user)
    {
        $validated = $request->validated();

        $user->update(['password' => Hash::make($validated['password'])]);

        return redirect()->back()
            ->with('success', __('messages.password_reset_successfully'));
    }
}
