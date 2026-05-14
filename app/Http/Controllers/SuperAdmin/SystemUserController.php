<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Merchant;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SystemUserController extends Controller
{
    /**
     * Display all system users
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by user type
        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
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

        $users = $query->with(['role', 'merchant'])->paginate(25);

        return view('super-admin.users.index', compact('users'));
    }

    /**
     * Show user creation form
     */
    public function create()
    {
        $merchants = Merchant::all();
        $roles = Role::all();
        return view('super-admin.users.create', compact('merchants', 'roles'));
    }

    /**
     * Store new user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'user_type' => 'required|in:super_admin,merchant_admin,employee,viewer',
            'merchant_id' => 'nullable|required_if:user_type,merchant_admin,employee,viewer|exists:merchants,id',
            'role_id' => 'nullable|exists:roles,id',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);

        User::create($validated);

        return redirect()->route('super-admin.users.index')
            ->with('success', 'User created successfully');
    }

    /**
     * Show user edit form
     */
    public function edit(User $user)
    {
        $merchants = Merchant::all();
        $roles = Role::all();
        return view('super-admin.users.edit', compact('user', 'merchants', 'roles'));
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'user_type' => 'required|in:super_admin,merchant_admin,employee,viewer',
            'merchant_id' => 'nullable|required_if:user_type,merchant_admin,employee,viewer|exists:merchants,id',
            'role_id' => 'nullable|exists:roles,id',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', $user->is_active);

        $user->update($validated);

        return redirect()->route('super-admin.users.index')
            ->with('success', 'User updated successfully');
    }

    /**
     * Delete user
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'Cannot delete your own account');
        }

        $user->delete();

        return redirect()->route('super-admin.users.index')
            ->with('success', 'User deleted successfully');
    }

    /**
     * Toggle user status
     */
    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'Cannot modify your own account');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';
        return redirect()->back()
            ->with('success', "User {$status} successfully");
    }

    /**
     * Inspect/impersonate a merchant - log in as that merchant
     */
    public function inspectMerchant(Merchant $merchant)
    {
        // Store the original super-admin ID in session for returning later
        Session::put('original_admin_id', auth()->id());
        Session::put('inspecting_merchant', $merchant->id);

        // Get or create a merchant admin user to log in as
        $merchantAdmin = User::where('merchant_id', $merchant->id)
            ->where('user_type', 'merchant_admin')
            ->first();

        if (!$merchantAdmin) {
            return redirect()->back()
                ->with('error', 'No merchant admin found for this merchant');
        }

        // Log in as the merchant admin
        Auth::login($merchantAdmin, true);
        Session::regenerate();

        return redirect()->route('dashboard')
            ->with('success', "Inspecting merchant: {$merchant->business_name}");
    }

    /**
     * Return to super-admin dashboard from inspection
     */
    public function exitInspection()
    {
        if (!Session::has('original_admin_id')) {
            return redirect()->route('login');
        }

        $originalAdminId = Session::pull('original_admin_id');
        Session::forget('inspecting_merchant');

        $superAdmin = User::find($originalAdminId);
        if ($superAdmin && $superAdmin->isSuperAdmin()) {
            Auth::login($superAdmin, true);
            Session::regenerate();

            return redirect()->route('super-admin.dashboard')
                ->with('success', 'Returned to super admin dashboard');
        }

        return redirect()->route('login');
    }
}
