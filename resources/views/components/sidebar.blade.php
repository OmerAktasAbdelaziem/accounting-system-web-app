<div class="component-sidebar" style="overflow-y:auto; overflow-x:hidden; max-height:calc(100vh - 140px); display:flex; flex-direction:column; gap:8px;">
    <style>
        .component-sidebar .sidebar-menu a { display:flex; gap:12px; align-items:center; width:100%; padding:8px; color:var(--primary-black); text-decoration:none; border-radius:6px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .component-sidebar .sidebar-menu a span { display:inline-block; max-width:180px; overflow:hidden; text-overflow:ellipsis; }
        .component-sidebar .sidebar-section { margin-top:8px; }
        .component-sidebar .sidebar-title { font-weight:800; font-size:12px; color:#666; padding:6px 0; text-transform:uppercase; }
        .component-sidebar .sidebar-logout { margin-top:auto; padding-top:10px; }
        .component-sidebar .sidebar-logout-link { display:flex; align-items:center; gap:8px; padding:10px; color:#c33; text-decoration:none; background:transparent; border-radius:6px; }
        .component-sidebar .sidebar-menu li { list-style:none; }
        .component-sidebar .sidebar-menu a.roles-management { position: relative; overflow: hidden; padding-right: 38px; }
        .component-sidebar .sidebar-menu a .roles-bg-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 28px;
            line-height: 1;
            color: rgba(255, 140, 0, 0.18);
            pointer-events: none;
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
    @if(!empty($menu['main']))
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
                        <span>{{ $label }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

    @if(!empty($menu['customers']))
        <div class="sidebar-section">
            <div class="sidebar-title">Customer Section</div>
            <ul class="sidebar-menu customers-menu">
                @foreach($menu['customers'] as $item)
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
                            <span>{{ $label }}</span>
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
                        $extraClass = trim($label === 'Roles Management' ? 'roles-management' : '');
                    @endphp
                    <li>
                        <a href="{{ $routeName ? route($routeName) : '#' }}" class="{{ trim(($active ? 'active' : '') . ' ' . $extraClass) }}">
                            @if($label === 'Roles Management')
                                <i class="bi bi-shield-lock-fill roles-bg-icon" aria-hidden="true"></i>
                            @endif
                            <i class="bi {{ $icon }}"></i>
                            <span>{{ $label }}</span>
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
                        $extraClass = trim($label === 'Roles Management' ? 'roles-management' : '');
                    @endphp
                    <li>
                        <a href="{{ $routeName ? route($routeName) : '#' }}" class="{{ trim(($active ? 'active' : '') . ' ' . $extraClass) }}">
                            @if($label === 'Roles Management')
                                <i class="bi bi-shield-lock-fill roles-bg-icon" aria-hidden="true"></i>
                            @endif
                            <i class="bi {{ $icon }}"></i>
                            <span>{{ $label }}</span>
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
                        $extraClass = trim($label === 'Roles Management' ? 'roles-management' : '');
                    @endphp
                    <li>
                        <a href="{{ $routeName ? route($routeName) : '#' }}" class="{{ trim(($active ? 'active' : '') . ' ' . $extraClass) }}">
                            @if($label === 'Roles Management')
                                <i class="bi bi-shield-lock-fill roles-bg-icon" aria-hidden="true"></i>
                            @endif
                            <i class="bi {{ $icon }}"></i>
                            <span>{{ $label }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="sidebar-logout">
        <a class="sidebar-logout-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="bi bi-box-arrow-left"></i> <span>{{ __('messages.logout') }}</span></a>
    </div>
</div>

