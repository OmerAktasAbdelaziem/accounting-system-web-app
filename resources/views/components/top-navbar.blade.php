@php
    $appName = \App\Models\Setting::getApplicationName();
    $dashboardRoute = auth()->user()?->isSuperAdmin() ? 'super-admin.dashboard' : 'dashboard';
@endphp
<link rel="stylesheet" href="{{ asset('css/top-navbar.css') }}">
<nav class="modern-top-navbar">
    <div class="brand">
        <a href="{{ route($dashboardRoute) }}" class="brand">
            <div class="logo-mark">A</div>
            <div class="app-name">{{ $appName }}</div>
        </a>
    </div>
    <div class="center">
        <div class="search">
            <i class="bi bi-search" style="color:rgba(255,255,255,0.6);margin-left:6px"></i>
            <input type="search" placeholder="Search pages, merchants, users..." aria-label="Search">
        </div>
    </div>
    <div class="actions">
        <button class="btn-quick" onclick="location.href='{{ route('super-admin.merchants.create') }}'">
            <i class="bi bi-plus-lg"></i>
            <span>Create</span>
        </button>
        <button class="icon-btn icon-badge" title="Notifications">
            <i class="bi bi-bell" style="font-size:18px"></i>
            <span class="badge">3</span>
        </button>
        <div class="user">
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
            @endphp
            <div class="avatar">
                @if($avatarUrl)
                    <img src="{{ $avatarUrl }}" alt="{{ auth()->user()->name }}">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'U') }}&background=ff6b35&color=fff&rounded=true" alt="{{ auth()->user()->name }}">
                @endif
            </div>
        </div>
    </div>
</nav>
