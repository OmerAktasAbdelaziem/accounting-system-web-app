@php
    $appName = \App\Models\Setting::getApplicationName();
@endphp
<style>
    .shared-top-navbar {
        background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
        color: #fff;
        padding: 0;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        border-bottom: 3px solid #ff6b35;
    }

    .shared-top-navbar .navbar-brand { font-weight:800; font-size:24px; color:#fff; display:flex; align-items:center; gap:10px; margin:0 20px; }
    .shared-top-navbar .navbar-end { margin-left:auto; display:flex; align-items:center; gap:20px; padding-right:20px; }
    .shared-top-navbar .user-avatar { width:40px; height:40px; border-radius:50%; background:#ff6b35; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:bold; }
    .shared-top-navbar .user-avatar-image { width:40px; height:40px; border-radius:50%; object-fit:cover; border:2px solid rgba(255,255,255,0.65); }
</style>

<nav class="shared-top-navbar">
    <div class="d-flex align-items-center w-100">
        @php
            $dashboardRoute = auth()->user()?->isSuperAdmin() ? 'super-admin.dashboard' : 'dashboard';
        @endphp
        <a href="{{ route($dashboardRoute) }}" class="navbar-brand">
            <span>{{ $appName }}</span>
        </a>

        <div class="navbar-end">
            @if(session('inspecting_merchant'))
                <form action="{{ route('super-admin.exit-inspection') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-warning btn-sm me-3" title="Exit merchant inspection and return to super admin">
                        <i class="bi bi-arrow-left"></i> Exit Inspection
                    </button>
                </form>
            @endif
            <div class="user-menu d-flex align-items-center gap-3">
                @php
                    $avatarPath = auth()->user()->profile_photo_path;
                    $avatarUrl = null;
                    if ($avatarPath) {
                        if (\Illuminate\Support\Facades\File::exists(public_path($avatarPath))) {
                            $avatarUrl = asset($avatarPath);
                        } elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($avatarPath)) {
                            $avatarUrl = asset('storage/' . ltrim($avatarPath, '/'));
                        } elseif (\Illuminate\Support\Facades\File::exists(public_path('storage/' . ltrim($avatarPath, '/')))) {
                            $avatarUrl = asset('storage/' . ltrim($avatarPath, '/'));
                        } else {
                            $avatarUrl = asset($avatarPath);
                        }
                    }
                    $initial = strtoupper(substr(auth()->user()->name ?? 'U', 0, 1));
                @endphp
                @if($avatarUrl)
                    <img src="{{ $avatarUrl }}" alt="{{ auth()->user()->name }}" class="user-avatar-image">
                @else
                    <div class="user-avatar">{{ $initial }}</div>
                @endif
                <div>
                    <small class="d-block" style="color: rgba(255,255,255,0.8);">{{ auth()->user()->name }}</small>
                    <small style="color:#ff6b35; font-weight:600;">{{ auth()->user()->isSuperAdmin() ? 'Super Admin' : (auth()->user()->isMerchantAdmin() ? 'Merchant Admin' : '') }}</small>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="display:inline; margin-left:10px;">
                    @csrf
                    <button type="submit" class="nav-link btn btn-link p-0 m-0" style="text-decoration:none; color:inherit;"><i class="bi bi-box-arrow-right"></i></button>
                </form>
            </div>
        </div>
    </div>
</nav>
