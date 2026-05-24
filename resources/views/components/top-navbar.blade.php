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
        <button id="notifButton" class="icon-btn icon-badge" title="Notifications" aria-expanded="false" aria-controls="notifDropdown">
            <i class="bi bi-bell" style="font-size:18px"></i>
            <span id="notifBadge" class="badge" style="display:none">0</span>
                @if(session()->has('original_admin_id'))
                    <form action="{{ route('super-admin.exit-inspection') }}" method="POST" style="display:inline;margin-right:10px;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-warning" title="Exit inspection">Exit Inspection</button>
                    </form>
                @endif
        <div id="notifDropdown" class="notif-dropdown" style="display:none" role="menu" aria-label="Notifications">
            <div class="notif-dropdown-head">
                <div>
                    <div class="notif-title">Notifications</div>
                    <div class="notif-subtitle">Live updates from your workspace</div>
                </div>
                <button id="markAllReadBtn" class="notif-mark-read" type="button">Mark all read</button>
            </div>
            <div id="notifList" class="notif-list"></div>
        </div>
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
    @php
        $merchantName = $subscription_block_details['merchant'] ?? 'Merchant';
        $merchantInitials = collect(explode(' ', $merchantName))->filter()->take(2)->map(function ($part) {
            return mb_strtoupper(mb_substr($part, 0, 1));
        })->implode('');
        if ($merchantInitials === '') {
            $merchantInitials = 'M';
        }
        $supportEmail = 'contact.us.omer@gmail.com';
        $supportWhatsapp = '905070493081';
    @endphp
    <div id="subscription-overlay" class="sub-lock-overlay" role="dialog" aria-modal="true" aria-labelledby="subLockTitle">
        <div class="sub-lock-modal">
            <div class="sub-lock-head">
                <div class="sub-lock-icon sub-lock-avatar" aria-hidden="true">{{ $merchantInitials }}</div>
                <div>
                    <h3 id="subLockTitle" class="sub-lock-title">Subscription Ended</h3>
                    <p class="sub-lock-subtitle">Access is temporarily locked until reactivation.</p>
                </div>
            </div>

            <div class="sub-lock-meta">
                <div class="sub-lock-chip">
                    <span>Merchant</span>
                    <strong>{{ $subscription_block_details['merchant'] ?? 'Unknown merchant' }}</strong>
                </div>
                <div class="sub-lock-chip">
                    <span>Ended On</span>
                    <strong>{{ $subscription_block_details['expires_at'] ?? 'N/A' }}</strong>
                </div>
                <div class="sub-lock-chip sub-lock-chip-alert">
                    <span>Status</span>
                    <strong>Deactivated</strong>
                </div>
            </div>

            <div class="sub-lock-actions">
                <div class="sub-lock-actions-left">
                    <a class="sub-lock-icon-btn" href="mailto:{{ $supportEmail }}" title="Email support" aria-label="Email support">
                        <i class="bi bi-envelope-paper"></i>
                    </a>
                    <a class="sub-lock-icon-btn" href="https://wa.me/{{ $supportWhatsapp }}" target="_blank" rel="noopener" title="WhatsApp support" aria-label="WhatsApp support">
                        <i class="bi bi-whatsapp"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script>
        (function(){
            // Freeze page scrolling while the subscription lock is active.
            document.documentElement.style.overflow = 'hidden';
            document.body.style.overflow = 'hidden';
            document.documentElement.style.height = '100%';
            document.body.style.height = '100%';
            document.body.style.touchAction = 'none';
        })();
    </script>
    <style>
        body > *:not(#subscription-overlay){
            pointer-events: none !important;
            user-select: none !important;
        }
    </style>
@endif
<script>
    (function(){
        // Client-side deterrents only; they do not provide real security.
        // They reduce casual access to devtools / source shortcuts across the app.
        document.addEventListener('contextmenu', function(e){
            e.preventDefault();
        }, true);

        document.addEventListener('keydown', function(e){
            const key = (e.key || '').toLowerCase();
            const blocked =
                e.key === 'F12' ||
                (e.ctrlKey && e.shiftKey && ['i', 'j', 'c'].includes(key)) ||
                (e.ctrlKey && key === 'u') ||
                (e.metaKey && e.altKey && ['i', 'j', 'c'].includes(key));

            if (blocked) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        }, true);

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

        function formatRelativeTime(dateInput) {
            if (!dateInput) return 'Just now';
            const date = new Date(dateInput);
            if (Number.isNaN(date.getTime())) return 'Just now';
            const seconds = Math.floor((Date.now() - date.getTime()) / 1000);
            if (seconds < 60) return 'Just now';
            const minutes = Math.floor(seconds / 60);
            if (minutes < 60) return `${minutes}m ago`;
            const hours = Math.floor(minutes / 60);
            if (hours < 24) return `${hours}h ago`;
            const days = Math.floor(hours / 24);
            return `${days}d ago`;
        }

        function normalizeNotification(raw) {
            const data = raw?.data || {};
            const typeText = (raw?.type || 'Update').replace(/Notification$/i, '');
            const title = data.title || data.subject || typeText || 'Notification';
            const message = data.message || data.details || data.description || 'You have a new update.';
            const ctaUrl = data.url || data.link || null;
            const ctaText = data.action_text || 'Open';
            const createdAt = raw?.created_at || raw?.createdAt || data.created_at || new Date().toISOString();
            const readAt = raw?.read_at || raw?.readAt || null;

            const lowerType = String(typeText).toLowerCase();
            let icon = 'bi-bell';
            let tone = 'info';

            if (lowerType.includes('subscription') || String(message).toLowerCase().includes('subscription')) {
                icon = 'bi-calendar-check';
                tone = 'subscription';
            } else if (lowerType.includes('payment') || String(message).toLowerCase().includes('pay')) {
                icon = 'bi-credit-card-2-front';
                tone = 'payment';
            } else if (lowerType.includes('warning') || String(message).toLowerCase().includes('expire')) {
                icon = 'bi-exclamation-triangle';
                tone = 'warning';
            } else if (lowerType.includes('success') || String(message).toLowerCase().includes('extended')) {
                icon = 'bi-check-circle';
                tone = 'success';
            }

            return {
                id: raw?.id || `${Date.now()}-${Math.random()}`,
                type: typeText,
                title,
                message,
                ctaUrl,
                ctaText,
                createdAt,
                readAt,
                isUnread: typeof raw?.isUnread === 'boolean' ? raw.isUnread : !readAt,
                icon,
                tone,
            };
        }

        function renderNotificationCard(raw) {
            const n = normalizeNotification(raw);
            return `
                <article class="notif-item notif-tone-${n.tone} ${n.isUnread ? 'notif-unread' : 'notif-read'}" data-id="${n.id}">
                    <div class="notif-icon-wrap">
                        <i class="bi ${n.icon}"></i>
                    </div>
                    <div class="notif-content">
                        <div class="notif-meta">
                            <span class="notif-chip">${n.type}</span>
                            <time class="notif-time">${formatRelativeTime(n.createdAt)}</time>
                        </div>
                        <h4 class="notif-item-title">${n.title}</h4>
                        <p class="notif-item-message">${n.message}</p>
                        <div class="notif-actions-row">
                            ${n.ctaUrl ? `<a class="notif-link" href="${n.ctaUrl}">${n.ctaText} <i class="bi bi-arrow-up-right"></i></a>` : '<span></span>'}
                            ${n.isUnread ? `<button class="notif-dismiss-btn" type="button" data-dismiss-id="${n.id}" title="Mark as read">Dismiss</button>` : ''}
                        </div>
                    </div>
                </article>
            `;
        }

        function isToday(dateInput){
            const d = new Date(dateInput);
            if (Number.isNaN(d.getTime())) return false;
            const now = new Date();
            return d.getFullYear() === now.getFullYear() && d.getMonth() === now.getMonth() && d.getDate() === now.getDate();
        }

        function isYesterday(dateInput){
            const d = new Date(dateInput);
            if (Number.isNaN(d.getTime())) return false;
            const today = new Date();
            const startToday = new Date(today.getFullYear(), today.getMonth(), today.getDate());
            const startYesterday = new Date(startToday);
            startYesterday.setDate(startYesterday.getDate() - 1);
            return d >= startYesterday && d < startToday;
        }

        function isThisWeek(dateInput){
            const d = new Date(dateInput);
            if (Number.isNaN(d.getTime())) return false;
            const now = new Date();
            const day = now.getDay();
            // Week starts on Monday
            const diffToMonday = (day === 0 ? -6 : 1 - day);
            const startOfWeek = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            startOfWeek.setDate(startOfWeek.getDate() + diffToMonday);
            const startOfToday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            return d >= startOfWeek && d < startOfToday;
        }

        function renderGroupedNotifications(list){
            const today = [];
            const yesterday = [];
            const thisWeek = [];
            const older = [];

            const sorted = [...list].sort((a, b) => {
                const ad = new Date(a?.created_at || a?.createdAt || 0).getTime();
                const bd = new Date(b?.created_at || b?.createdAt || 0).getTime();
                return bd - ad;
            });

            sorted.forEach(item => {
                const dt = item?.created_at || item?.createdAt;
                if (isToday(dt)) today.push(item);
                else if (isYesterday(dt)) yesterday.push(item);
                else if (isThisWeek(dt)) thisWeek.push(item);
                else older.push(item);
            });

            const sections = [];
            if (today.length) {
                sections.push(`<div class="notif-group"><div class="notif-group-title">Today</div>${today.map(renderNotificationCard).join('')}</div>`);
            }
            if (yesterday.length) {
                sections.push(`<div class="notif-group"><div class="notif-group-title">Yesterday</div>${yesterday.map(renderNotificationCard).join('')}</div>`);
            }
            if (thisWeek.length) {
                sections.push(`<div class="notif-group"><div class="notif-group-title">This Week</div>${thisWeek.map(renderNotificationCard).join('')}</div>`);
            }
            if (older.length) {
                sections.push(`<div class="notif-group"><div class="notif-group-title">Older</div>${older.map(renderNotificationCard).join('')}</div>`);
            }
            return sections.join('');
        }

        function handleIncomingNotification(n){
            try{
                // increment badge
                const current = parseInt(notifBadge.textContent || '0') || 0;
                notifBadge.textContent = current + 1;
                notifBadge.style.display = 'inline-block';
                // prepend to dropdown
                const notifList = document.getElementById('notifList');
                const html = renderNotificationCard(n);
                if (notifList) {
                    let todayGroup = Array.from(notifList.querySelectorAll('.notif-group')).find(g => g.querySelector('.notif-group-title')?.textContent?.trim() === 'Today');
                    if (!todayGroup) {
                        notifList.insertAdjacentHTML('afterbegin', `<div class="notif-group"><div class="notif-group-title">Today</div></div>`);
                        todayGroup = notifList.querySelector('.notif-group');
                    }

                    todayGroup.insertAdjacentHTML('beforeend', html);
                    const added = todayGroup.lastElementChild;
                    if (added) added.classList.add('notif-enter');
                }
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
                const notifList = document.getElementById('notifList');
                if (notifList) {
                    const items = renderGroupedNotifications(json.notifications || []);
                    notifList.innerHTML = items || '<div class="notif-empty"><i class="bi bi-bell-slash"></i><span>No notifications yet</span></div>';
                }
            }catch(e){ console.error(e); }
        }

        async function markOneAsRead(id){
            try{
                await fetch(`{{ route('notifications.markRead') }}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ ids: [id] })
                });

                const card = document.querySelector(`.notif-item[data-id="${id}"]`);
                if (card) {
                    card.classList.remove('notif-unread');
                    card.classList.add('notif-read');
                    const btn = card.querySelector('.notif-dismiss-btn');
                    if (btn) btn.remove();
                }

                const current = parseInt(notifBadge.textContent || '0') || 0;
                const next = Math.max(0, current - 1);
                notifBadge.textContent = String(next);
                notifBadge.style.display = next > 0 ? 'inline-block' : 'none';
            } catch (e) {
                console.error(e);
            }
        }

        async function markAllAsRead(){
            try{
                await fetch(`{{ route('notifications.markRead') }}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({})
                });
                notifBadge.style.display = 'none';
                notifBadge.textContent = '0';
                refreshNotifs();
            } catch (e) {
                console.error(e);
            }
        }

        notifBtn && notifBtn.addEventListener('click', function(e){
            if(notifDropdown.style.display === 'block'){
                notifDropdown.style.display='none';
                notifBtn.setAttribute('aria-expanded', 'false');
            } else {
                notifDropdown.style.display='block';
                notifBtn.setAttribute('aria-expanded', 'true');
                refreshNotifs();
            }
        });

        const markAllReadBtn = document.getElementById('markAllReadBtn');
        markAllReadBtn && markAllReadBtn.addEventListener('click', markAllAsRead);

        const notifList = document.getElementById('notifList');
        notifList && notifList.addEventListener('click', function(e){
            const target = e.target;
            const dismissBtn = target.closest('[data-dismiss-id]');
            if (dismissBtn) {
                e.preventDefault();
                markOneAsRead(dismissBtn.getAttribute('data-dismiss-id'));
            }
        });

        // Avatar dropdown
        const avatarBtn = document.getElementById('avatarButton');
        const avatarDropdown = document.getElementById('avatarDropdown');
        avatarBtn && avatarBtn.addEventListener('click', function(){ avatarDropdown.style.display = avatarDropdown.style.display === 'block' ? 'none' : 'block'; });

        // Close dropdowns on outside click
        document.addEventListener('click', function(e){
            const target = e.target;
            if(!document.getElementById('globalSearch')?.contains(target)) resultsBox.style.display='none';
            if(!notifBtn.contains(target) && !notifDropdown.contains(target)) {
                notifDropdown.style.display='none';
                notifBtn.setAttribute('aria-expanded', 'false');
            }
            if(!avatarBtn.contains(target) && !avatarDropdown.contains(target)) avatarDropdown.style.display='none';
        });

        // Initial notifications poll. If Echo is available we'll still do an initial fetch
        refreshNotifs();
        if (!echoEnabled) {
            setInterval(refreshNotifs, 30000);
        }
    })();
</script>
