<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Comprobante {{ $orden->codigo }} · {{ config('taller.nombre') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --ink: #0f172a;
            --ink-soft: #1e293b;
            --muted: #64748b;
            --line: #e2e8f0;
            --line-soft: #edf1f6;
            --canvas: #f4f6f9;
            --brand: #d97706;
            --brand-strong: #b45309;
            --brand-bright: #f59e0b;
            --brand-soft: #fffbeb;
            --font-sans: 'Barlow', system-ui, -apple-system, 'Segoe UI', sans-serif;
            --font-mono: 'JetBrains Mono', ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 28px 16px 56px;
            background:
                linear-gradient(rgba(15, 23, 42, .025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(15, 23, 42, .025) 1px, transparent 1px),
                #eef2f7;
            background-size: 28px 28px;
            color: var(--ink-soft);
            font-family: var(--font-sans);
            font-size: 12px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        .print-toolbar {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 8px;
            width: min(100%, 820px);
            margin: 0 auto 14px;
        }
        .btn-print,
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 15px;
            border-radius: 6px;
            font-family: inherit;
            font-size: 11.5px;
            font-weight: 600;
            letter-spacing: .01em;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-print {
            border: 1px solid var(--brand);
            background: var(--brand-bright);
            color: #111827;
            box-shadow: 0 1px 2px rgba(180, 83, 9, .22);
        }
        .btn-print:hover, .btn-print:focus { background: var(--brand); color: #111827; }
        .btn-back {
            border: 1px solid #cbd5e1;
            background: #fff;
            color: #475569;
        }
        .btn-back:hover, .btn-back:focus { border-color: #94a3b8; color: var(--ink); }

        .sheet {
            position: relative;
            width: min(100%, 820px);
            margin: 0 auto;
            padding: 36px 40px 28px;
            border: 1px solid var(--line);
            border-top: 3px solid var(--brand-bright);
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 10px 30px rgba(15, 23, 42, .08);
        }

        .doc-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 24px;
            padding-bottom: 18px;
            border-bottom: 2px solid var(--ink);
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .brand-icon {
            width: 52px;
            height: 52px;
            display: grid;
            place-items: center;
            border: 1px solid #1f2937;
            border-radius: 6px;
            background: #111827;
            color: #fbbf24;
            font-family: var(--font-mono);
            font-size: 19px;
            font-weight: 600;
            letter-spacing: .04em;
        }
        .brand-name {
            margin: 0;
            color: var(--ink);
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -.025em;
        }
        .brand-tagline {
            margin: 2px 0 0;
            color: var(--muted);
            font-family: var(--font-mono);
            font-size: 8.5px;
            font-weight: 500;
            letter-spacing: .16em;
            text-transform: uppercase;
        }
        .brand-meta {
            margin-top: 8px;
            color: var(--muted);
            font-size: 10px;
            line-height: 1.6;
        }

        .doc-box {
            min-width: 240px;
            padding: 14px 16px;
            border: 1px solid var(--line);
            border-top: 3px solid var(--brand-bright);
            border-radius: 6px;
            background: #f8fafc;
            text-align: right;
        }
        .doc-label {
            color: #475569;
            font-family: var(--font-mono);
            font-size: 8.5px;
            font-weight: 500;
            letter-spacing: .14em;
            text-transform: uppercase;
        }
        .doc-code {
            margin: 4px 0 8px;
            color: var(--ink);
            font-family: var(--font-mono);
            font-size: 18px;
            font-weight: 600;
            letter-spacing: -.03em;
        }
        .doc-state {
            display: inline-block;
            padding: 3px 8px;
            border: 1px solid #1f2937;
            border-radius: 4px;
            background: #111827;
            color: #fde68a;
            font-family: var(--font-mono);
            font-size: 8.5px;
            font-weight: 500;
            letter-spacing: .06em;
            text-transform: uppercase;
        }
        .doc-dates {
            margin-top: 10px;
            color: #475569;
            font-size: 10px;
        }
        .doc-dates div {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 2px 0;
        }
        .doc-dates strong {
            color: var(--ink);
            font-family: var(--font-mono);
            font-size: 9.5px;
            font-weight: 500;
        }

        .section { padding-top: 20px; }
        .section-title {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 10px;
            color: var(--ink);
            font-family: var(--font-mono);
            font-size: 9.5px;
            font-weight: 600;
            letter-spacing: .12em;
            text-transform: uppercase;
        }
        .section-title::before {
            content: '';
            width: 4px;
            height: 12px;
            border-radius: 1px;
            background: var(--brand-bright);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }
        .info-card {
            padding: 13px 15px;
            border: 1px solid var(--line);
            border-left: 3px solid #cbd5e1;
            border-radius: 4px;
            background: #f8fafc;
        }
        .info-card h3 {
            margin: 0 0 6px;
            color: var(--muted);
            font-family: var(--font-mono);
            font-size: 8.5px;
            font-weight: 500;
            letter-spacing: .1em;
            text-transform: uppercase;
        }
        .info-card p {
            margin: 0;
            color: var(--ink);
            font-size: 12px;
            font-weight: 600;
        }
        .info-card small {
            display: block;
            margin-top: 2px;
            color: var(--muted);
            font-size: 10px;
            font-weight: 400;
        }

        .items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2px;
        }
        .items th {
            padding: 7px 10px;
            border-top: 1px solid var(--line-strong);
            border-bottom: 1px solid var(--line-strong);
            background: #f8fafc;
            color: #475569;
            font-family: var(--font-mono);
            font-size: 8.5px;
            font-weight: 500;
            letter-spacing: .08em;
            text-transform: uppercase;
            text-align: left;
        }
        .items td {
            padding: 8px 10px;
            border-bottom: 1px solid var(--line-soft);
            vertical-align: top;
        }
        .items tr:last-child td { border-bottom: 0; }
        .items .num {
            font-family: var(--font-mono);
            font-size: 10.5px;
            text-align: right;
            white-space: nowrap;
        }
        .items .item-name { color: var(--ink); font-weight: 600; }
        .items .item-meta {
            color: var(--muted);
            font-family: var(--font-mono);
            font-size: 8.5px;
        }

        .totals-wrap {
            display: flex;
            justify-content: flex-end;
            margin-top: 16px;
        }
        .totals {
            width: 300px;
            padding: 14px 16px;
            border: 1px solid #1e293b;
            border-radius: 6px;
            background:
                repeating-linear-gradient(135deg, rgba(255, 255, 255, .022) 0 1px, transparent 1px 11px),
                #111827;
            color: #e2e8f0;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            padding: 4px 0;
            font-size: 10.5px;
        }
        .totals-row strong {
            color: #fff;
            font-family: var(--font-mono);
            font-size: 10px;
            font-weight: 500;
        }
        .totals-total {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid rgba(255, 255, 255, .18);
            color: #fbbf24;
            font-family: var(--font-mono);
            font-size: 16px;
            font-weight: 600;
            letter-spacing: -.02em;
        }

        .notes {
            margin-top: 18px;
            padding: 12px 15px;
            border-left: 3px solid var(--brand-bright);
            border-radius: 0 4px 4px 0;
            background: var(--brand-soft);
            color: #78350f;
            font-size: 10.5px;
        }
        .notes strong {
            display: block;
            margin-bottom: 2px;
            font-family: var(--font-mono);
            font-size: 8.5px;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .signatures {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 50px;
            margin-top: 48px;
        }
        .signature {
            padding-top: 8px;
            border-top: 1px solid #94a3b8;
            color: var(--muted);
            font-family: var(--font-mono);
            font-size: 8.5px;
            text-align: center;
        }

        .doc-footer {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 8px;
            margin-top: 26px;
            padding-top: 11px;
            border-top: 1px solid var(--line);
            color: #94a3b8;
            font-family: var(--font-mono);
            font-size: 8px;
        }

        @media (max-width: 640px) {
            body { padding: 16px 10px 32px; }
            .sheet { padding: 22px 18px 20px; }
            .doc-header { flex-direction: column; }
            .doc-box { width: 100%; text-align: left; }
            .info-grid, .signatures { grid-template-columns: 1fr; }
            .signatures { gap: 30px; }
            .totals { width: 100%; }
            .items { display: block; overflow-x: auto; }
        }

        @media print {
            @page { size: A4; margin: 12mm; }

            body {
                padding: 0;
                background: #fff;
                font-size: 10.5px;
            }
            .print-toolbar { display: none !important; }
            .sheet {
                width: 100%;
                margin: 0;
                padding: 0;
                border: 0;
                border-radius: 0;
                box-shadow: none;
            }
            .doc-header { flex-direction: row; }
            .doc-box { width: auto; text-align: right; }
            .info-grid, .signatures { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .totals { width: 300px; }
            .items { display: table; overflow: visible; }
            .section, .totals-wrap, .signatures { break-inside: avoid; }
            .brand-icon,
            .doc-box,
            .doc-state,
            .totals,
            .notes,
            .info-card,
            .items th {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="print-toolbar">
        <a href="{{ route('ordenes.show', $orden) }}" class="btn-back">← Volver al detalle</a>
        <button type="button" class="btn-print" onclick="window.print()">Imprimir comprobante</button>
    </div>

    <main class="sheet">
        <header class="doc-header">
            <div class="brand">
                <span class="brand-icon">TM</span>
                <div>
                    <h1 class="brand-name">{{ config('taller.nombre') }}</h1>
                    <p class="brand-tagline">Servicio automotriz · Gestión integral</p>
                    <div class="brand-meta">
                        {{ config('taller.direccion') }}<br>
                        Tel. {{ config('taller.telefono') }} · {{ config('taller.email') }}<br>
                        RUC {{ config('taller.ruc') }}
                    </div>
                </div>
            </div>

            <div class="doc-box">
                <div class="doc-label">Orden de servicio</div>
                <div class="doc-code">{{ $orden->codigo }}</div>
                <span class="doc-state">{{ $orden->estado }}</span>
                <div class="doc-dates">
                    <div><span>Ingreso</span><strong>{{ $orden->fecha_ingreso->format('d/m/Y') }}</strong></div>
                    <div><span>Entrega estimada</span><strong>{{ $orden->fecha_estimada_entrega?->format('d/m/Y') ?? '—' }}</strong></div>
                    <div><span>Entrega real</span><strong>{{ $orden->fecha_entrega_real?->format('d/m/Y') ?? 'Pendiente' }}</strong></div>
                    <div><span>Emitido</span><strong>{{ now()->format('d/m/Y H:i') }}</strong></div>
                </div>
            </div>
        </header>

        <section class="section">
            <h2 class="section-title">Cliente y vehículo</h2>
            <div class="info-grid">
                <div class="info-card">
                    <h3>Propietario</h3>
                    <p>{{ $orden->vehiculo->cliente->nombre_completo }}</p>
                    <small>{{ $orden->vehiculo->cliente->tipo_documento }} {{ $orden->vehiculo->cliente->documento }}</small>
                    <small>{{ $orden->vehiculo->cliente->telefono ?: 'Teléfono no registrado' }}</small>
                    @if ($orden->vehiculo->cliente->email)
                        <small>{{ $orden->vehiculo->cliente->email }}</small>
                    @endif
                </div>
                <div class="info-card">
                    <h3>Vehículo</h3>
                    <p>{{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }} · {{ $orden->vehiculo->anio }}</p>
                    <small>Placa: {{ $orden->vehiculo->placa }}</small>
                    <small>Color: {{ $orden->vehiculo->color ?: 'No registrado' }} · Kilometraje: {{ number_format($orden->vehiculo->kilometraje) }} km</small>
                </div>
            </div>
        </section>

        @if ($orden->diagnostico)
            <section class="section">
                <h2 class="section-title">Diagnóstico</h2>
                <div class="info-card">
                    <p style="font-weight: 500; white-space: pre-line;">{{ $orden->diagnostico }}</p>
                </div>
            </section>
        @endif

        <section class="section">
            <h2 class="section-title">Servicios de mano de obra</h2>
            <table class="items">
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th class="num">Cant.</th>
                        <th class="num">P. unitario</th>
                        <th class="num">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orden->servicios as $servicio)
                        <tr>
                            <td>
                                <div class="item-name">{{ $servicio->nombre }}</div>
                                <div class="item-meta">{{ $servicio->duracion_estimada ?: 'Duración no definida' }}</div>
                            </td>
                            <td class="num">{{ $servicio->pivot->cantidad }}</td>
                            <td class="num">S/ {{ number_format((float) $servicio->pivot->precio_unitario, 2) }}</td>
                            <td class="num">S/ {{ number_format((float) $servicio->pivot->subtotal, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="color: #94a3b8;">Sin servicios registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <section class="section">
            <h2 class="section-title">Repuestos utilizados</h2>
            <table class="items">
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th class="num">Cant.</th>
                        <th class="num">P. unitario</th>
                        <th class="num">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orden->repuestos as $repuesto)
                        <tr>
                            <td>
                                <div class="item-name">{{ $repuesto->nombre }}</div>
                                <div class="item-meta">{{ $repuesto->codigo }} · {{ $repuesto->marca ?: 'Sin marca' }}</div>
                            </td>
                            <td class="num">{{ $repuesto->pivot->cantidad }}</td>
                            <td class="num">S/ {{ number_format((float) $repuesto->pivot->precio_unitario, 2) }}</td>
                            <td class="num">S/ {{ number_format((float) $repuesto->pivot->subtotal, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="color: #94a3b8;">Sin repuestos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <div class="totals-wrap">
            <div class="totals">
                <div class="totals-row"><span>Mano de obra</span><strong>S/ {{ number_format($subtotalServicios, 2) }}</strong></div>
                <div class="totals-row"><span>Repuestos</span><strong>S/ {{ number_format($subtotalRepuestos, 2) }}</strong></div>
                <div class="totals-total"><span>Total</span><span>{{ $orden->total_formateado }}</span></div>
            </div>
        </div>

        @if ($orden->observaciones)
            <div class="notes">
                <strong>Observaciones</strong>
                <span style="white-space: pre-line;">{{ $orden->observaciones }}</span>
            </div>
        @endif

        <div class="signatures">
            <div class="signature">Firma del cliente</div>
            <div class="signature">Responsable del taller</div>
        </div>

        <footer class="doc-footer">
            <span>Registrada por: {{ $orden->usuario?->name ?? 'Usuario no disponible' }}</span>
            <span>Documento generado por {{ config('taller.nombre') }} · {{ now()->format('d/m/Y H:i') }}</span>
        </footer>
    </main>
</body>
</html>
