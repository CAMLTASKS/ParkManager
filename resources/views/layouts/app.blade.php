<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="portal-sync-url" content="{{ route('portal.sync.run') }}">
    <meta name="portal-sync-interval" content="{{ $portalSyncInterval ?? 5 }}">
    <meta name="barcode-entry-url" content="{{ route('entry') }}">
    <title>{{ $pageTitle ?? $parkName }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
</head>
<body class="app-shell">
    @include('partials.flash-modal')
    <div class="panel-shell">
        @include('partials.sidebar')
        <main class="panel-main">
            <header class="panel-header">
                <div class="header-copy">
                    <small>{{ $parkName }}</small>
                    <h1>{{ $pageTitle }}</h1>
                    <p>{{ $pageSubtitle }}</p>
                </div>
                <div class="header-actions">
                    @yield('header_actions')
                    <span class="turn-badge">{{ $operator['shift'] }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="button button-muted" type="submit">Salir</button>
                    </form>
                </div>
            </header>

            @yield('content')
        </main>
    </div>
    <nav class="mobile-quick-nav mobile-app-nav">
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="mobile-nav-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 13h7V4H4v9Zm9 7h7v-9h-7v9Zm0-16v5h7V4h-7ZM4 20h7v-5H4v5Z"/></svg>
            </span>
            <span>Inicio</span>
        </a>
        <a href="{{ route('manage') }}" class="{{ request()->routeIs('manage') || request()->routeIs('transaction.show') ? 'active' : '' }}">
            <span class="mobile-nav-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M11 5H5v14h6v2H3V3h8v2Zm10 7-5-5-1.41 1.41L17.17 11H9v2h8.17l-2.58 2.59L16 17l5-5Z"/></svg>
            </span>
            <span>Gestion</span>
        </a>
        <a href="{{ route('entry') }}" class="{{ request()->routeIs('entry') ? 'active' : '' }}">
            <span class="mobile-nav-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 11H7.83l4.58-4.59L11 5l-7 7 7 7 1.41-1.41L7.83 13H19v-2Z"/></svg>
            </span>
            <span>Entrada</span>
        </a>
        @if ($currentUser->isAdmin())
            <a href="{{ route('settings') }}" class="{{ request()->routeIs('settings') || request()->routeIs('audit') || request()->routeIs('reports') || request()->routeIs('monthly.*') ? 'active' : '' }}">
                <span class="mobile-nav-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 1 3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4Zm1 6v5h4v2h-6V7h2Z"/></svg>
                </span>
                <span>Admin</span>
            </a>
        @else
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="mobile-nav-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 1 0 10 10A10.01 10.01 0 0 0 12 2Zm1 11h5v-2h-4V6h-2v7Z"/></svg>
                </span>
                <span>Turno</span>
            </a>
        @endif
    </nav>
    <div class="loading-overlay" id="loadingOverlay" aria-hidden="true">
        <div class="parking-loader">
            <div class="loader-lane"></div>
            <div class="loader-bike">
                <span class="bike-seat"></span>
                <span class="bike-body"></span>
                <span class="bike-handle"></span>
                <span class="bike-wheel bike-wheel-front"></span>
                <span class="bike-wheel bike-wheel-back"></span>
            </div>
            <p>Procesando movimiento...</p>
        </div>
    </div>
    <script src="{{ asset('assets/js/app.js') }}"></script>
</body>
</html>
