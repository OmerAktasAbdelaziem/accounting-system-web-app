<div class="component-sidebar" style="overflow-y:auto; overflow-x:hidden; max-height:calc(100vh - 140px); display:flex; flex-direction:column; gap:8px;">
    <style>
        .component-sidebar .sidebar-menu a { display:flex; gap:12px; align-items:center; width:100%; padding:8px; color:var(--primary-black); text-decoration:none; border-radius:6px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .component-sidebar .sidebar-menu a span { display:inline-block; max-width:180px; overflow:hidden; text-overflow:ellipsis; }
        .component-sidebar .sidebar-section { margin-top:8px; }
        .component-sidebar .sidebar-title { font-weight:800; font-size:12px; color:#666; padding:6px 0; text-transform:uppercase; }
        .component-sidebar .sidebar-logout { margin-top:auto; padding-top:10px; }
        .component-sidebar .sidebar-logout-link { display:flex; align-items:center; gap:8px; padding:10px; color:#c33; text-decoration:none; background:transparent; border-radius:6px; }
        .component-sidebar .sidebar-menu li { list-style:none; }

        @media (max-width: 768px) {
            .component-sidebar {
                max-height: none;
                padding: 8px 4px 18px;
                gap: 6px;
            }

            .component-sidebar .sidebar-menu a {
                padding: 12px 10px;
                font-size: 14px;
            }

            .component-sidebar .sidebar-menu a span {
                max-width: calc(100vw - 110px);
            }

            .component-sidebar .sidebar-section {
                margin-top: 4px;
            }

            .component-sidebar .sidebar-title {
                padding: 4px 0 2px;
            }

            .component-sidebar .sidebar-logout {
                padding-top: 8px;
            }
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

    @if($branchScope)
        <div class="sidebar-section">
            <div class="sidebar-title">Branch scope</div>
            <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill text-white" style="background: linear-gradient(135deg, rgba(255,140,0,0.92), rgba(255,179,71,0.92)); box-shadow: 0 10px 22px rgba(255,140,0,0.18);">
                <i class="bi bi-diagram-3-fill"></i>
                <span class="fw-semibold">{{ $branchScope['label'] }}</span>
            </div>
        </div>
    @endif

    @if($isSuperAdmin)
        <div class="sidebar-section">
            <div class="sidebar-title">Dashboard Tools</div>
            <ul class="sidebar-menu main-menu">
                <li>
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Live Dashboard</span>
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
            <div class="sidebar-title">Operations</div>
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
            <div class="sidebar-title">Systems Section</div>
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
            <div class="sidebar-title">Reports Section</div>
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
            <div class="sidebar-title">Admin User Section</div>
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

