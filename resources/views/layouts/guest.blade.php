<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#111827">
    <title>@yield('title', 'Acceso') · Taller Mecánico</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/acceso.css') }}?v={{ filemtime(public_path('css/acceso.css')) }}" rel="stylesheet">

    @stack('styles')
</head>
<body>
    <div class="auth-page">
        <section class="auth-brand" aria-label="Información del sistema">
            <a href="{{ route('login') }}" class="auth-logo">
                <span class="auth-logo-icon"><i class="bi bi-wrench-adjustable"></i></span>
                <span>
                    <span class="auth-logo-name">Taller Mecánico</span>
                    <span class="auth-logo-sub">Control de operaciones</span>
                </span>
            </a>

            <div class="auth-brand-content">
                <span class="auth-eyebrow">Sistema de gestión de taller</span>
                <h1>Cada orden, repuesto y costo <span>bajo control.</span></h1>
                <p>Del ingreso del vehículo a la entrega final: órdenes, mano de obra, inventario valorizado y reportes en una sola operación ordenada.</p>

                <div class="auth-specs" aria-label="Módulos principales">
                    <div class="auth-spec">
                        <span class="auth-spec-num">01 / OPERACIÓN</span>
                        <span class="auth-spec-title">Órdenes de servicio</span>
                        <span class="auth-spec-meta">Ingreso, diagnóstico y estados</span>
                    </div>
                    <div class="auth-spec">
                        <span class="auth-spec-num">02 / ALMACÉN</span>
                        <span class="auth-spec-title">Inventario</span>
                        <span class="auth-spec-meta">Stock crítico y reposición</span>
                    </div>
                    <div class="auth-spec">
                        <span class="auth-spec-num">03 / CONTROL</span>
                        <span class="auth-spec-title">Reportes</span>
                        <span class="auth-spec-meta">Ingresos y productividad</span>
                    </div>
                </div>
            </div>

            <div class="auth-brand-footer">
                <span><i class="bi bi-database-check me-1"></i>PostgreSQL</span>
                <span><i class="bi bi-braces me-1"></i>Laravel 12</span>
                <span><i class="bi bi-shield-check me-1"></i>Sesiones seguras</span>
            </div>
        </section>

        <main class="auth-panel">
            <div class="auth-card">
                @yield('content')
            </div>

            <div class="auth-panel-note">
                <i class="bi bi-lock-fill"></i>Conexión segura · Acceso restringido al personal
            </div>
            <p class="auth-copyright">© {{ now()->year }} Taller Mecánico · Sistema de gestión</p>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
