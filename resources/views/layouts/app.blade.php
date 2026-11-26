<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#111827">
    <meta name="color-scheme" content="light">
    <title>@yield('title', 'Panel') · Taller Mecánico</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/taller.css') }}?v={{ filemtime(public_path('css/taller.css')) }}" rel="stylesheet">

    @stack('styles')
</head>
<body>
    <a href="#contenido" class="skip-link">Saltar al contenido</a>

    <div class="app-shell">
        <aside class="sidebar" id="sidebar" aria-label="Menú lateral">
            <a href="{{ route('dashboard') }}" class="sidebar-brand">
                <span class="brand-icon"><i class="bi bi-wrench-adjustable"></i></span>
                <span>
                    <span class="brand-name">Taller Mecánico</span>
                    <span class="brand-sub">Control de operaciones</span>
                </span>
            </a>

            <div class="sidebar-section">Operación</div>
            <nav class="sidebar-nav" aria-label="Navegación de operación">
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif>
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>Panel de control</span>
                </a>
                <a href="{{ route('ordenes.index') }}" class="sidebar-link {{ request()->routeIs('ordenes.*') ? 'active' : '' }}" @if(request()->routeIs('ordenes.*')) aria-current="page" @endif>
                    <i class="bi bi-clipboard2-check-fill"></i>
                    <span>Órdenes de servicio</span>
                </a>
                <a href="{{ route('clientes.index') }}" class="sidebar-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}" @if(request()->routeIs('clientes.*')) aria-current="page" @endif>
                    <i class="bi bi-people-fill"></i>
                    <span>Clientes</span>
                </a>
                <a href="{{ route('vehiculos.index') }}" class="sidebar-link {{ request()->routeIs('vehiculos.*') ? 'active' : '' }}" @if(request()->routeIs('vehiculos.*')) aria-current="page" @endif>
                    <i class="bi bi-car-front-fill"></i>
                    <span>Vehículos</span>
                </a>
            </nav>

            <div class="sidebar-section">Catálogo e inventario</div>
            <nav class="sidebar-nav" aria-label="Navegación de catálogo">
                <a href="{{ route('servicios.index') }}" class="sidebar-link {{ request()->routeIs('servicios.*') ? 'active' : '' }}" @if(request()->routeIs('servicios.*')) aria-current="page" @endif>
                    <i class="bi bi-wrench-adjustable"></i>
                    <span>Servicios</span>
                </a>
                <a href="{{ route('repuestos.index') }}" class="sidebar-link {{ request()->routeIs('repuestos.*') ? 'active' : '' }}" @if(request()->routeIs('repuestos.*')) aria-current="page" @endif>
                    <i class="bi bi-box-seam-fill"></i>
                    <span>Repuestos</span>
                </a>
            </nav>

            <div class="sidebar-section">Control y reportes</div>
            <nav class="sidebar-nav" aria-label="Navegación de control">
                <a href="{{ route('reportes.index') }}" class="sidebar-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}" @if(request()->routeIs('reportes.*')) aria-current="page" @endif>
                    <i class="bi bi-bar-chart-line-fill"></i>
                    <span>Reportes</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                @auth
                    <div class="sidebar-user">
                        <div class="d-flex align-items-center gap-2">
                            <span class="user-avatar">{{ auth()->user()->iniciales }}</span>
                            <div class="min-w-0 flex-grow-1">
                                <div class="user-name text-truncate">{{ auth()->user()->name }}</div>
                                <span class="badge-soft {{ auth()->user()->rol_badge }}">{{ auth()->user()->rol_label }}</span>
                            </div>
                        </div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="sidebar-logout">
                                <i class="bi bi-box-arrow-right"></i>Cerrar sesión
                            </button>
                        </form>
                    </div>
                @endauth

                <div class="sidebar-system">
                    <span class="status-dot"></span>
                    <div class="min-w-0">
                        <div class="system-title">Sistema conectado</div>
                        <div class="system-meta">PostgreSQL · Laravel</div>
                    </div>
                </div>
                <div class="sidebar-footnote">
                    <span>TM / {{ now()->year }}</span>
                    <span>v1.0</span>
                </div>
            </div>
        </aside>

        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <div class="main-wrapper">
            <header class="topbar">
                <button class="btn btn-icon d-lg-none" id="sidebarToggle" type="button" aria-label="Abrir menú" aria-controls="sidebar" aria-expanded="false">
                    <i class="bi bi-list fs-5"></i>
                </button>

                <div class="page-heading flex-grow-1 min-w-0">
                    <span class="page-accent" aria-hidden="true"></span>
                    <div class="min-w-0">
                        <div class="page-eyebrow text-truncate">@yield('eyebrow', 'Taller mecánico')</div>
                        <h1 class="page-title text-truncate">@yield('title', 'Panel de control')</h1>
                    </div>
                </div>

                <div class="topbar-actions">
                    <span class="topbar-date d-none d-xl-inline-flex">
                        <i class="bi bi-calendar3"></i>
                        {{ now()->locale('es')->translatedFormat('d M Y') }}
                    </span>

                    <a href="{{ route('ordenes.create') }}" class="btn btn-brand btn-sm" title="Registrar nueva orden de servicio">
                        <i class="bi bi-plus-lg"></i>
                        <span class="d-none d-sm-inline ms-1">Nueva orden</span>
                    </a>

                    @auth
                        <div class="topbar-user">
                            <span class="user-avatar">{{ auth()->user()->iniciales }}</span>
                            <div class="topbar-user-meta">
                                <div class="topbar-user-name text-truncate">{{ auth()->user()->name }}</div>
                                <span class="badge-soft {{ auth()->user()->rol_badge }}">{{ auth()->user()->rol_label }}</span>
                            </div>
                        </div>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-icon topbar-logout" title="Cerrar sesión" aria-label="Cerrar sesión">
                                <i class="bi bi-box-arrow-right"></i>
                            </button>
                        </form>
                    @endauth
                </div>
            </header>

            <main class="content" id="contenido">
                <header class="print-doc-header" aria-hidden="true">
                    <div class="print-doc-brand">
                        <span class="print-doc-mark">TM</span>
                        <div>
                            <div class="print-doc-name">{{ config('taller.nombre') }}</div>
                            <div class="print-doc-meta">{{ config('taller.direccion') }}</div>
                            <div class="print-doc-meta">RUC {{ config('taller.ruc') }} · Tel. {{ config('taller.telefono') }}</div>
                        </div>
                    </div>
                    <div class="print-doc-right">
                        <div class="print-doc-title">@yield('title', 'Panel de control')</div>
                        <div class="print-doc-eyebrow">@yield('eyebrow', 'Taller mecánico')</div>
                        <div class="print-doc-meta">Emitido {{ now()->format('d/m/Y H:i') }}</div>
                    </div>
                </header>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show app-alert" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show app-alert" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger app-alert" role="alert">
                        <div class="d-flex gap-2">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <div>
                                <div class="fw-bold mb-1">Revisa la información ingresada</div>
                                <ul class="mb-0 ps-3 small">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="page-intro">
                    <p class="page-subtitle">@yield('subtitle', 'Gestiona la operación diaria del taller.')</p>
                    <div class="page-actions">@yield('actions')</div>
                </div>

                @yield('content')
            </main>

            <footer class="footer">
                <span>© {{ now()->year }} Taller Mecánico · Sistema de gestión</span>
                <span><i class="bi bi-database-check me-1"></i>PostgreSQL · Laravel 12</span>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
        const sidebarToggle = document.getElementById('sidebarToggle');

        const cerrarMenu = () => {
            sidebar?.classList.remove('open');
            sidebarBackdrop?.classList.remove('show');
            sidebarToggle?.setAttribute('aria-expanded', 'false');
        };

        sidebarToggle?.addEventListener('click', () => {
            const abierto = sidebar.classList.toggle('open');
            sidebarBackdrop.classList.toggle('show', abierto);
            sidebarToggle.setAttribute('aria-expanded', abierto ? 'true' : 'false');
        });

        sidebarBackdrop?.addEventListener('click', cerrarMenu);
        window.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') cerrarMenu();
        });

        document.querySelectorAll('.alert-dismissible').forEach((alert) => {
            window.setTimeout(() => {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            }, 7000);
        });
    </script>
    @stack('scripts')
</body>
</html>
