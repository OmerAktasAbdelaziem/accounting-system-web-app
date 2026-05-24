@php
    $appName = \App\Models\Setting::getApplicationName();
    $dashboardRoute = auth()->user()?->isSuperAdmin() ? 'super-admin.dashboard' : 'dashboard';
@endphp
<link rel="stylesheet" href="{{ asset('css/top-navbar.css') }}">
<!-- Laravel Echo / Pusher (optional). Will be initialized if broadcasting keys are configured -->
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.11.3/dist/echo.iife.js"></script>
<nav class="modern-top-navbar">
    <div class="brand">
        <a href="{{ route($dashboardRoute) }}" class="brand">
            <div class="logo-mark">A</div>
            <div class="app-name">{{ $appName }}</div>
        </a>
    </div>
    <div class="center">
        <div class="search" id="globalSearch">
            <i class="bi bi-search" style="color:rgba(255,255,255,0.6);margin-left:6px"></i>
            <input id="globalSearchInput" type="search" placeholder="Search... (press Enter)" aria-label="Search">
            <div id="globalSearchResults" style="position:absolute;top:48px;left:50%;transform:translateX(-50%);background:#111;padding:8px;border-radius:8px;min-width:320px;display:none;z-index:2000;max-height:320px;overflow:auto"></div>
        </div>
    </div>
    <div class="actions">
        {{-- Create button removed per request --}}
        <button id="notifButton" class="icon-btn icon-badge" title="Notifications">
            <i class="bi bi-bell" style="font-size:18px"></i>
            <span id="notifBadge" class="badge" style="display:none">0</span>
        </button>
        <div id="notifDropdown" style="position:absolute;right:20px;top:60px;background:#111;padding:10px;border-radius:8px;min-width:300px;display:none;z-index:2000;max-height:360px;overflow:auto"></div>
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
            <div class="avatar" id="avatarButton" style="cursor:pointer">
                @if($avatarUrl)
                    <img src="{{ $avatarUrl }}" alt="{{ auth()->user()->name }}">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'U') }}&background=ff6b35&color=fff&rounded=true" alt="{{ auth()->user()->name }}">
                @endif
            </div>
            <div id="avatarDropdown" style="position:absolute;right:20px;top:60px;background:#111;padding:8px;border-radius:8px;min-width:200px;display:none;z-index:2000;color:#fff">
                <a href="{{ route('profile') }}" style="display:block;padding:8px;color:#fff;text-decoration:none">Profile</a>
                <a href="{{ route('settings.index') }}" style="display:block;padding:8px;color:#fff;text-decoration:none">System Settings</a>
                <form id="avatarLogoutForm" action="{{ route('logout') }}" method="POST" style="margin:0;padding:8px;">
                    @csrf
                    <button type="submit" class="btn btn-link text-white p-0" style="text-decoration:none">Logout</button>
                </form>
            </div>
        </div>
    </div>
</nav>
@if(!empty($subscription_blocked))
    <div id="subscription-overlay" style="position:fixed;inset:0;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);z-index:20000;display:flex;align-items:center;justify-content:center">
        <div style="background:#111;color:#fff;padding:28px;border-radius:12px;min-width:360px;max-width:720px;text-align:left;box-shadow:0 8px 32px rgba(0,0,0,0.6)">
            <h3 style="margin-top:0">Subscription Ended</h3>
            <p>Your subscription for <strong>{{ $subscription_block_details['merchant'] ?? '' }}</strong> has ended on <strong>{{ $subscription_block_details['expires_at'] ?? '' }}</strong>.</p>
            <p>Please contact support to reactivate your subscription and restore access.</p>
            <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:12px">
                <a href="{{ url('/contact') }}" class="btn btn-primary">Contact Support</a>
            </div>
        </div>
    </div>
    <style>
        body > *:not(#subscription-overlay){
            pointer-events: none !important;
            user-select: none !important;
        }
    </style>
@endif
<script>
    (function(){
        // Initialize Echo config from server values (if present)
        const LARAVEL_ECHO_KEY = @json(config('broadcasting.connections.pusher.key')) || null;
        const LARAVEL_ECHO_CLUSTER = @json(data_get(config('broadcasting.connections.pusher.options', []), 'cluster')) || @json(config('broadcasting.connections.pusher.cluster')) || null;
        const LARAVEL_BROADCAST_DRIVER = @json(config('broadcasting.default')) || null;
        // Debounce helper
        function debounce(fn, wait){
            let t;
            return function(){
                const args = arguments; clearTimeout(t); t = setTimeout(()=>fn.apply(null,args), wait);
            }
        }

        const input = document.getElementById('globalSearchInput');
        const resultsBox = document.getElementById('globalSearchResults');

        async function doSearch(q){
            if(!q || q.length < 2){ resultsBox.style.display='none'; return; }
            try{
                const res = await fetch(`{{ url('search') }}?q=${encodeURIComponent(q)}`, {headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}});
                const data = await res.json();
                const items = data.results || [];
                if(items.length===0){ resultsBox.innerHTML='<div style="padding:8px;color:#ccc">No results</div>'; resultsBox.style.display='block'; return; }
                resultsBox.innerHTML = items.map(it=>`<a href="${it.url}" style="display:block;padding:8px;color:#fff;text-decoration:none;border-bottom:1px solid rgba(255,255,255,0.03)"><strong style="text-transform:capitalize">${it.type}</strong> — ${it.title}</a>`).join('');
                resultsBox.style.display='block';
            }catch(e){ console.error(e); }
        }

        input && input.addEventListener('input', debounce(function(e){ doSearch(e.target.value); }, 300));
        input && input.addEventListener('keydown', function(e){ if(e.key === 'Escape'){ resultsBox.style.display='none'; } });

        // Notifications
        const notifBtn = document.getElementById('notifButton');
        const notifBadge = document.getElementById('notifBadge');
        const notifDropdown = document.getElementById('notifDropdown');

        // Real-time notifications via Echo (if configured). Falls back to polling.
        const currentUserId = {{ auth()->id() ?? 'null' }};
        let echoEnabled = false;

        function handleIncomingNotification(n){
            try{
                // increment badge
                const current = parseInt(notifBadge.textContent || '0') || 0;
                notifBadge.textContent = current + 1;
                notifBadge.style.display = 'inline-block';
                // prepend to dropdown
                const html = `<div style="padding:8px;border-bottom:1px solid rgba(255,255,255,0.03)"><small style="color:#999">${n.type || ''}</small><div>${(n.data?.title||n.data?.message||n.message||'Notification')}</div></div>`;
                notifDropdown.innerHTML = html + notifDropdown.innerHTML;
            }catch(e){ console.error(e); }
        }

        try{
            if(LARAVEL_ECHO_KEY && (LARAVEL_BROADCAST_DRIVER === 'pusher' || LARAVEL_BROADCAST_DRIVER === 'redis')){
                // Initialize Echo using Pusher
                window.Pusher = window.Pusher || Pusher;
                window.Echo = new Echo({
                    broadcaster: 'pusher',
                    key: LARAVEL_ECHO_KEY,
                    cluster: LARAVEL_ECHO_CLUSTER || undefined,
                    forceTLS: true,
                    auth: {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        }
                    }
                });

                if(window.Echo && currentUserId){
                    window.Echo.private(`App.Models.User.${currentUserId}`).notification(function(notification){
                        handleIncomingNotification(notification);
                    });
                    echoEnabled = true;
                }
            }
        }catch(e){
            console.warn('Echo init failed', e);
            echoEnabled = false;
        }

        async function refreshNotifs(){
            try{
                const res = await fetch(`{{ url('notifications') }}`, {headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}});
                const json = await res.json();
                const unread = json.unread || 0;
                if(unread>0){ notifBadge.style.display='inline-block'; notifBadge.textContent = unread; } else { notifBadge.style.display='none'; }
                // prepare dropdown list
                notifDropdown.innerHTML = (json.notifications || []).map(n=>`<div style="padding:8px;border-bottom:1px solid rgba(255,255,255,0.03)"><small style="color:#999">${n.type}</small><div>${(n.data.title||n.data.message||'Notification')}</div></div>`).join('') || '<div style="padding:8px;color:#ccc">No notifications</div>';
            }catch(e){ console.error(e); }
        }

        notifBtn && notifBtn.addEventListener('click', function(e){
            if(notifDropdown.style.display === 'block'){ notifDropdown.style.display='none'; } else { notifDropdown.style.display='block'; refreshNotifs(); }
        });

        // Avatar dropdown
        const avatarBtn = document.getElementById('avatarButton');
        const avatarDropdown = document.getElementById('avatarDropdown');
        avatarBtn && avatarBtn.addEventListener('click', function(){ avatarDropdown.style.display = avatarDropdown.style.display === 'block' ? 'none' : 'block'; });

        // Close dropdowns on outside click
        document.addEventListener('click', function(e){
            const target = e.target;
            if(!document.getElementById('globalSearch')?.contains(target)) resultsBox.style.display='none';
            if(!notifBtn.contains(target) && !notifDropdown.contains(target)) notifDropdown.style.display='none';
            if(!avatarBtn.contains(target) && !avatarDropdown.contains(target)) avatarDropdown.style.display='none';
        });

        // Initial notifications poll. If Echo is available we'll still do an initial fetch
        refreshNotifs();
        if (!echoEnabled) {
            setInterval(refreshNotifs, 30000);
        }
    })();
</script>
