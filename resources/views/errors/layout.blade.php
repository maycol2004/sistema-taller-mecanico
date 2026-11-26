<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <meta name="theme-color" content="#111827">
    <title>@yield('title', 'Error') · Taller Mecánico</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/taller.css') }}?v={{ filemtime(public_path('css/taller.css')) }}" rel="stylesheet">

    <style>
        body {
            display: grid;
            place-items: center;
            padding: 1.5rem;
            background:
                linear-gradient(rgba(15, 23, 42, .028) 1px, transparent 1px),
                linear-gradient(90deg, rgba(15, 23, 42, .028) 1px, transparent 1px),
                var(--canvas);
            background-size: 28px 28px;
        }
        .error-card {
            width: min(100%, 690px);
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: var(--radius);
            background: var(--surface);
            box-shadow: var(--shadow-hover);
        }
        .error-top {
            position: relative;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1.5rem;
            padding: 1.5rem 1.75rem 1.35rem;
            border-bottom: 3px solid var(--brand-bright);
            background:
                repeating-linear-gradient(135deg, rgba(255, 255, 255, .022) 0 1px, transparent 1px 11px),
                linear-gradient(135deg, #111827, #0f172a);
        }
        .error-brand {
            display: inline-flex;
            align-items: center;
            gap: .7rem;
            color: #f8fafc;
            text-decoration: none;
        }
        .error-brand:hover { color: #fde68a; }
        .error-brand-icon {
            display: grid;
            place-items: center;
            width: 46px;
            height: 46px;
            border: 1px solid rgba(245, 158, 11, .45);
            border-radius: 6px;
            background: #0b1220;
            color: #fbbf24;
            font-size: 1.15rem;
        }
        .error-brand-name {
            display: block;
            color: #f8fafc;
            font-size: .98rem;
            font-weight: 700;
            letter-spacing: -.01em;
            line-height: 1.2;
        }
        .error-brand-sub {
            display: block;
            margin-top: 2px;
            color: #94a3b8;
            font-family: var(--font-mono);
            font-size: .59rem;
            font-weight: 500;
            letter-spacing: .14em;
            text-transform: uppercase;
        }
        .error-code-wrap { text-align: right; }
        .error-code-label {
            color: #94a3b8;
            font-family: var(--font-mono);
            font-size: .6rem;
            font-weight: 500;
            letter-spacing: .18em;
            text-transform: uppercase;
        }
        .error-code {
            margin-top: .15rem;
            color: #fbbf24;
            font-family: var(--font-mono);
            font-size: clamp(2.9rem, 9vw, 4.3rem);
            font-weight: 600;
            letter-spacing: -.08em;
            line-height: .9;
        }
        .error-body { padding: 1.8rem 1.75rem 1.6rem; }
        .error-icon {
            display: grid;
            place-items: center;
            width: 50px;
            height: 50px;
            border: 1px solid var(--brand-line);
            border-radius: 8px;
            background: var(--brand-soft);
            color: var(--brand-strong);
            font-size: 1.35rem;
        }
        .error-kicker {
            color: var(--muted);
            font-family: var(--font-mono);
            font-size: .62rem;
            font-weight: 500;
            letter-spacing: .1em;
            text-transform: uppercase;
        }
        .error-body h1 {
            margin: 1rem 0 .55rem;
            color: var(--ink);
            font-size: clamp(1.4rem, 4vw, 1.75rem);
            font-weight: 800;
            letter-spacing: -.035em;
        }
        .error-body p {
            max-width: 52ch;
            margin: 0 0 1.5rem;
            color: var(--muted);
            font-size: .93rem;
            line-height: 1.65;
        }
        .error-body p .badge-soft { margin-left: .15rem; }
        .error-footer {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
            padding: .85rem 1.75rem;
            border-top: 1px solid var(--line-soft);
            background: #f8fafc;
            color: var(--muted);
            font-family: var(--font-mono);
            font-size: .6rem;
            letter-spacing: .05em;
            text-transform: uppercase;
        }
        @media (max-width: 575.98px) {
            body { padding: .85rem; }
            .error-top { padding: 1.2rem 1.25rem 1.1rem; }
            .error-body { padding: 1.4rem 1.25rem 1.25rem; }
            .error-footer { padding: .8rem 1.25rem; }
            .error-brand-icon { width: 40px; height: 40px; }
        }
        @media print {
            body { display: block; padding: 0; background: #fff; }
            .error-card { border: 0; box-shadow: none; }
            .error-top { color: #111827; background: #fff; border-bottom: 2px solid #111827; }
            .error-brand-name { color: #111827; }
            .error-code, .error-brand-icon { color: #111827; }
            .error-brand-icon { border-color: #111827; background: #fff; }
            .error-body .btn { display: none !important; }
            .error-footer { background: #fff; }
        }
    </style>
</head>
<body>
    <main class="error-card">
        <header class="error-top">
            <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="error-brand">
                <span class="error-brand-icon"><i class="bi bi-wrench-adjustable"></i></span>
                <span>
                    <span class="error-brand-name">Taller Mecánico</span>
                    <span class="error-brand-sub">Control de operaciones</span>
                </span>
            </a>
            <div class="error-code-wrap">
                <div class="error-code-label">Código de error</div>
                <div class="error-code">@yield('code', '500')</div>
            </div>
        </header>

        <div class="error-body">
            <div class="d-flex align-items-center gap-2">
                <div class="error-icon"><i class="bi bi-@yield('icon', 'exclamation-triangle')"></i></div>
                <span class="error-kicker">@yield('eyebrow', 'Incidencia del sistema')</span>
            </div>

            <h1>@yield('title', 'Ocurrió un error')</h1>
            <p>@yield('message', 'No fue posible completar la solicitud.')</p>

            <div class="d-flex flex-wrap gap-2">
                @yield('actions')
            </div>
        </div>

        <footer class="error-footer">
            <span>TM / {{ now()->year }} · Sistema de gestión</span>
            <span>Taller Mecánico</span>
        </footer>
    </main>
</body>
</html>
