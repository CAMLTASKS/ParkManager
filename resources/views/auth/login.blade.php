@extends('layouts.guest')

@section('content')
<div class="auth-layout">
    <section class="auth-showcase">
        <div class="showcase-brand">
            <div class="brand-mark">P</div>
            <div>
                <strong>{{ $parkName }}</strong>
                <span>{{ $appName }}</span>
            </div>
        </div>

        <div class="showcase-copy">
            <h1>Operacion simple para un parqueadero que se mueve rapido.</h1>
            <p>Controla entradas, salidas, pagos pendientes y auditoria con una interfaz clara en escritorio y una experiencia agil en celular.</p>
        </div>

        <div class="showcase-card">
            <div class="caption">Vista operativa</div>
            <img src="{{ asset('assets/images/fondo.png') }}" alt="Vista del sistema">
        </div>

        <div class="showcase-metrics">
            <article>
                <span>Gestion centralizada</span>
                <strong>1 sola vista</strong>
            </article>
            <article>
                <span>Operacion movil</span>
                <strong>Rapida y simple</strong>
            </article>
        </div>
    </section>

    <section class="auth-form-panel">
        <div class="auth-card">
            <div class="auth-card-top">
                <small>{{ $parkName }}</small>
                <h2>Iniciar sesion</h2>
                <p>Ingresa con tu usuario y contrasena para comenzar el turno.</p>
            </div>

            <form method="POST" action="{{ route('login.attempt') }}" class="stack-lg" data-loading-form>
                @csrf
                <label>
                    <span>Usuario o correo</span>
                    <input type="text" name="login" value="{{ old('login') }}" placeholder="operador o email" autofocus>
                </label>

                <label>
                    <span>Contrasena</span>
                    <input type="password" name="password" placeholder="Tu contrasena">
                </label>

                <label class="remember-row">
                    <input type="checkbox" name="remember" value="1">
                    <span>Recordar sesion</span>
                </label>

                <button type="submit" class="button button-primary button-block">Ingresar al sistema</button>
            </form>

            <div class="auth-footer auth-footer-block">
                <span class="pill success">Acceso seguro</span>
                <small>Desarrollado por IngeDev Solutions</small>
            </div>
        </div>
    </section>
</div>
@endsection
