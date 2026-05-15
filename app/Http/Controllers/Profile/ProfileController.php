<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        return view('profile.show', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:500',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('profile_photo')) {
            if (Schema::hasColumn('users', 'profile_photo_path')) {
                if ($user->profile_photo_path) {
                    // Cleanup old copies from both public and storage-backed paths.
                    if (File::exists(public_path($user->profile_photo_path))) {
                        File::delete(public_path($user->profile_photo_path));
                    }
                    if (File::exists(public_path('storage/' . ltrim($user->profile_photo_path, '/')))) {
                        File::delete(public_path('storage/' . ltrim($user->profile_photo_path, '/')));
                    }
                    Storage::disk('public')->delete($user->profile_photo_path);
                }

                $folder = public_path('profile-photos');
                if (!File::isDirectory($folder)) {
                    File::makeDirectory($folder, 0755, true);
                }

                $extension = strtolower($request->file('profile_photo')->getClientOriginalExtension());
                $filename = 'avatar_' . now()->format('YmdHis') . '_' . mt_rand(1000, 9999) . '.' . $extension;
                $request->file('profile_photo')->move($folder, $filename);

                $validated['profile_photo_path'] = 'profile-photos/' . $filename;
            }
        }

        $user->update($validated);
        return back()->with('success', 'Profile updated successfully!');
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        auth()->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password changed successfully!');
    }
}
