<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $parkName ?? config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
</head>
<body class="guest-shell">
    @include('partials.flash-modal')
    @yield('content')
    <div class="loading-overlay" id="loadingOverlay" aria-hidden="true">
        <div class="parking-loader">
            <div class="loader-lane"></div>
            <div class="loader-car">
                <span></span><span></span><span></span>
            </div>
            <p>Ingresando al parqueadero...</p>
        </div>
    </div>
    <script src="{{ asset('assets/js/app.js') }}"></script>
</body>
</html>
