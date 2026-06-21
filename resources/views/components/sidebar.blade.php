<div class="component-sidebar">
    <style>
        /* Hide mobile-only header by default; show only on small screens */
        .component-sidebar .sidebar-mobile-head { display: none; }
        /* Keep component CSS mobile-only to avoid overriding desktop sidebar styles */
        @media (max-width: 992px) {
            .component-sidebar .sidebar-mobile-head { display:flex; }
            .component-sidebar { color:#f5f3ee; overflow-y:auto; overflow-x:hidden; height:100%; max-height:none; display:flex; flex-direction:column; gap:8px; padding:10px; border-radius:20px; background: linear-gradient(180deg, #23211d 0%, #191917 100%); }
            .component-sidebar .sidebar-mobile-head { display:flex; align-items:center; justify-content:space-between; gap:10px; padding: 4px 4px 10px; margin-bottom: 2px; border-bottom: 1px solid rgba(255,255,255,0.06); }
            .component-sidebar .sidebar-mobile-title { font-size: 14px; font-weight: 900; letter-spacing: 0.02em; color: #fff; }
            .component-sidebar .sidebar-mobile-close { border: 1px solid rgba(255,140,0,0.22); background: rgba(255,140,0,0.10); color: #ff8c00; width: 38px; height: 38px; border-radius: 12px; display:inline-flex; align-items:center; justify-content:center; }

            .component-sidebar .sidebar-menu a { display:flex; gap:12px; align-items:center; width:100%; padding:12px; color:#f5f3ee; text-decoration:none; border-radius:14px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; background:rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.05); }
            .component-sidebar .sidebar-menu a span { display:inline-block; max-width:calc(86vw - 110px); overflow:hidden; text-overflow:ellipsis; }
            .component-sidebar .sidebar-menu a:hover { background:rgba(255,255,255,0.06); }
            .component-sidebar .sidebar-menu a.active { background:linear-gradient(135deg, rgba(255,140,0,0.24), rgba(20,184,166,0.18)); border-color:rgba(255,140,0,0.24); }
            .component-sidebar .sidebar-section { margin-top:8px; padding:10px; border:1px solid rgba(255,255,255,0.06); border-radius:18px; background:rgba(255,255,255,0.02); }
            .component-sidebar .sidebar-title { font-weight:800; font-size:12px; color:rgba(245,243,238,0.72); padding:6px 4px 10px; text-transform:uppercase; letter-spacing:0.08em; }
            .component-sidebar .sidebar-logout { margin-top:auto; padding-top:10px; }
            .component-sidebar .sidebar-logout-link { display:flex; align-items:center; gap:8px; padding:12px; color:#ffd9b0; text-decoration:none; background:rgba(255,140,0,0.08); border-radius:14px; border:1px solid rgba(255,140,0,0.16); }
            .component-sidebar .sidebar-menu li { list-style:none; }
        }
    </style>

    @php
        $flattenFirstString = function ($value) {
            if (is_string($value)) {
                $trimmed = trim($value);
                return $trimmed !== '' ? $trimmed : null;
            }

            if (is_array($value)) {
                $stack = array_values($value);
                while (!empty($stack)) {
                    $item = array_shift($stack);
                    if (is_string($item)) {
                        $trimmed = trim($item);
                        if ($trimmed !== '') {
                            return $trimmed;
                        }
                    } elseif (is_array($item)) {
                        foreach ($item as $nested) {
                            $stack[] = $nested;
                        }
                    }
                }
            }

            return null;
        };

        $resolveLabel = function ($rawLabel) use ($flattenFirstString) {
            if (is_array($rawLabel)) {
                $fromArray = $flattenFirstString($rawLabel);
                return $fromArray ?? 'N/A';
            }

            if (!is_string($rawLabel)) {
                return $rawLabel === null ? '' : (string) $rawLabel;
            }

            $trans = __($rawLabel);
            if (is_array($trans)) {
                $fromArray = $flattenFirstString($trans);
                if ($fromArray !== null) return $fromArray;
            }
            if ($trans !== $rawLabel) return (string) $trans;
            $lower = strtolower($rawLabel);
            $try = __($lower);
            if ($try !== $lower) {
                if (is_array($try)) {
                    $fromArray = $flattenFirstString($try);
                    if ($fromArray !== null) return $fromArray;
                }
                return (string) $try;
            }
            return $rawLabel;
        };
    @endphp

    @php
        $currentUser = auth()->user();
        $branchScope = $currentUser ? $currentUser->branchAccessSummary() : null;
    @endphp

    <div class="sidebar-mobile-head">
        <div class="sidebar-mobile-title">{{ __('messages.menu') }}</div>
        <button type="button" class="sidebar-mobile-close" aria-label="Close sidebar" onclick="return window.__toggleMobileSidebar(event)">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    @if($branchScope)
        <div class="sidebar-section">
            <div class="sidebar-title">{{ __('messages.branch_scope') }}</div>
            <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill text-white" style="background: linear-gradient(135deg, rgba(255,140,0,0.92), rgba(255,179,71,0.92)); box-shadow: 0 10px 22px rgba(255,140,0,0.18);">
                <i class="bi bi-diagram-3-fill"></i>
                <span class="fw-semibold">{{ $resolveLabel($branchScope['label'] ?? '') }}</span>
            </div>
        </div>
    @endif

    @if($isSuperAdmin)
        <div class="sidebar-section">
            <div class="sidebar-title">{{ __('messages.dashboard_tools') }}</div>
            <ul class="sidebar-menu main-menu">
                <li>
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>{{ __('messages.live_dashboard') }}</span>
                    </a>
                </li>
                {{-- Removed Feature Access and New Merchant per admin request --}}
            </ul>
        </div>
    @endif

    @if(!empty($menu['main']) && !$isSuperAdmin)
        <ul class="sidebar-menu main-menu">
            @foreach($menu['main'] as $item)
                @php
                    $label = $resolveLabel($item['label'] ?? '');
                    if (is_array($label)) { $label = json_encode($label); }
                    $routeName = is_string($item['route'] ?? null) ? $item['route'] : '#';
                    $icon = is_string($item['icon'] ?? null) ? $item['icon'] : 'bi-circle';
                    $active = $routeName && request()->routeIs(str_replace('.index','*',$routeName));
                    $extraClass = trim($label === 'Roles Management' ? 'roles-management' : '');
                @endphp
                <li>
                    <a href="{{ $routeName ? route($routeName) : '#' }}" class="{{ trim(($active ? 'active' : '') . ' ' . $extraClass) }}">
                        @if($label === 'Roles Management')
                            <i class="bi bi-shield-lock-fill roles-bg-icon" aria-hidden="true"></i>
                        @endif
                        <i class="bi {{ $icon }}"></i>
                        <span>{{ is_array($label) ? json_encode($label) : $label }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

    @if(!empty($menu['customers']))
        <div class="sidebar-section">
            <div class="sidebar-title">{{ __('messages.operations') }}</div>
            <ul class="sidebar-menu customers-menu">
                @foreach($menu['customers'] as $item)
                    @php
                        $label = $resolveLabel($item['label'] ?? '');
                        if (is_array($label)) { $label = json_encode($label); }
                        $routeName = is_string($item['route'] ?? null) ? $item['route'] : '#';
                        $icon = is_string($item['icon'] ?? null) ? $item['icon'] : 'bi-circle';
                        $active = $routeName && request()->routeIs(str_replace('.index','*',$routeName));
                    @endphp
                    <li>
                        <a href="{{ $routeName ? route($routeName) : '#' }}" class="{{ $active ? 'active' : '' }}">
                            <i class="bi {{ $icon }}"></i>
                            <span>{{ is_array($label) ? json_encode($label) : $label }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(!empty($menu['systems']))
        <div class="sidebar-section">
            <div class="sidebar-title">{{ __('messages.systems_section') }}</div>
            <ul class="sidebar-menu systems-menu">
                @foreach($menu['systems'] as $item)
                    @php
                        $label = $resolveLabel($item['label'] ?? '');
                        if (is_array($label)) { $label = json_encode($label); }
                        $routeName = is_string($item['route'] ?? null) ? $item['route'] : '#';
                        $icon = is_string($item['icon'] ?? null) ? $item['icon'] : 'bi-circle';
                        $active = $routeName && request()->routeIs(str_replace('.index','*',$routeName));
                    @endphp
                    <li>
                        <a href="{{ $routeName ? route($routeName) : '#' }}" class="{{ $active ? 'active' : '' }}">
                            <i class="bi {{ $icon }}"></i>
                            <span>{{ is_array($label) ? json_encode($label) : $label }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(!empty($menu['reports']))
        <div class="sidebar-section">
            <div class="sidebar-title">{{ __('messages.reports_section') }}</div>
            <ul class="sidebar-menu reports-menu">
                @foreach($menu['reports'] as $item)
                    @php
                        $label = $resolveLabel($item['label'] ?? '');
                        if (is_array($label)) { $label = json_encode($label); }
                        $routeName = is_string($item['route'] ?? null) ? $item['route'] : '#';
                        $icon = is_string($item['icon'] ?? null) ? $item['icon'] : 'bi-circle';
                        $active = $routeName && request()->routeIs(str_replace('.index','*',$routeName));
                    @endphp
                    <li>
                        <a href="{{ $routeName ? route($routeName) : '#' }}" class="{{ $active ? 'active' : '' }}">
                            <i class="bi {{ $icon }}"></i>
                            <span>{{ is_array($label) ? json_encode($label) : $label }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(!empty($menu['admin']))
        <div class="sidebar-section">
            <div class="sidebar-title">{{ __('messages.admin_user_section') }}</div>
            <ul class="sidebar-menu admin-menu">
                @foreach($menu['admin'] as $item)
                    @php
                        $label = $resolveLabel($item['label'] ?? '');
                        if (is_array($label)) { $label = json_encode($label); }
                        $routeName = is_string($item['route'] ?? null) ? $item['route'] : '#';
                        $icon = is_string($item['icon'] ?? null) ? $item['icon'] : 'bi-circle';
                        $active = $routeName && request()->routeIs(str_replace('.index','*',$routeName));
                    @endphp
                    <li>
                        <a href="{{ $routeName ? route($routeName) : '#' }}" class="{{ $active ? 'active' : '' }}">
                            <i class="bi {{ $icon }}"></i>
                            <span>{{ is_array($label) ? json_encode($label) : $label }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="sidebar-logout">
        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="sidebar-logout-link btn btn-link p-0 m-0" style="text-decoration:none; color:inherit;"><i class="bi bi-box-arrow-left"></i> <span>{{ __('messages.logout') }}</span></button>
        </form>
    </div>
</div>

