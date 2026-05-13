@php
    $menu = [
        [
            'label' => 'Dashboard',
            'route' => 'dashboard',
            'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 13h7V4H4v9Zm9 7h7v-9h-7v9Zm0-16v5h7V4h-7ZM4 20h7v-5H4v5Z"/></svg>',
        ],
        [
            'label' => 'Gestion',
            'route' => 'manage',
            'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M11 5H5v14h6v2H3V3h8v2Zm10 7-5-5-1.41 1.41L17.17 11H9v2h8.17l-2.58 2.59L16 17l5-5Z"/></svg>',
        ],
        [
            'label' => 'Entrada',
            'route' => 'entry',
            'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 11H7.83l4.58-4.59L11 5l-7 7 7 7 1.41-1.41L7.83 13H19v-2Z"/></svg>',
        ],
        [
            'label' => 'Reportes',
            'route' => 'reports',
            'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 19h14v2H3V3h2v16Zm3-4H6v-5h2v5Zm5 0h-2V7h2v8Zm5 0h-2V4h2v11Z"/></svg>',
            'admin' => true,
        ],
        [
            'label' => 'Mensualidades',
            'route' => 'monthly.index',
            'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 2h10v2h3v18H4V4h3V2Zm2 2h6V3H9v1Zm-3 4v12h12V8H6Zm2 3h8v2H8v-2Zm0 4h5v2H8v-2Z"/></svg>',
            'admin' => true,
        ],
        [
            'label' => 'Configuracion',
            'route' => 'settings',
            'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m19.14 12.94.04-.94-.04-.94 2.03-1.58-1.92-3.32-2.39.96a7.04 7.04 0 0 0-1.63-.94L14.96 2h-3.92l-.27 2.18c-.58.22-1.12.52-1.63.94l-2.39-.96-1.92 3.32 2.03 1.58-.04.94.04.94-2.03 1.58 1.92 3.32 2.39-.96c.5.41 1.05.72 1.63.94L11.04 22h3.92l.27-2.18c.58-.22 1.12-.52 1.63-.94l2.39.96 1.92-3.32-2.03-1.58ZM13 15h-2v-2h2v2Zm0-4h-2V6h2v5Z"/></svg>',
            'admin' => true,
        ],
        [
            'label' => 'Auditoria',
            'route' => 'audit',
            'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 1 3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4Zm1 6v5h4v2h-6V7h2Z"/></svg>',
            'admin' => true,
        ],
    ];
@endphp

<aside class="sidebar" id="sidebar">
    <div class="brand-block">
        <button class="ghost-icon sidebar-toggle" type="button" id="sidebarToggle">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16v2H4V7Zm0 8h16v2H4v-2Z"/></svg>
        </button>
        <div class="brand">
            <div class="brand-mark">E</div>
            <div class="brand-copy">
                <strong>{{ $parkName }}</strong>
                <span>{{ $appName }}</span>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">
        @foreach ($menu as $item)
            @continue(($item['admin'] ?? false) && ! $currentUser->isAdmin())
            <a href="{{ route($item['route']) }}" class="nav-link {{ request()->routeIs($item['route']) ? 'active' : '' }}">
                <span class="nav-icon nav-icon-svg">{!! $item['icon'] !!}</span>
                <span class="nav-label">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="sidebar-user">
        <div class="avatar">{{ strtoupper(substr($operator['name'], 0, 1)) }}</div>
        <div class="user-copy">
            <strong>{{ $operator['name'] }}</strong>
            <span>{{ $operator['role'] }}</span>
        </div>
    </div>
</aside>
