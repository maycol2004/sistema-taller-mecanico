@extends('layouts.app')

@section('title', 'Reportes')
@section('eyebrow', 'Análisis del taller')
@section('subtitle', 'Consulta los ingresos, la productividad y el estado del inventario en el periodo que elijas.')

@section('actions')
    <a href="{{ route('reportes.exportar', ['desde' => $desde->toDateString(), 'hasta' => $hasta->toDateString()]) }}" class="btn btn-brand btn-sm">
        <i class="bi bi-file-earmark-excel me-1"></i>Exportar CSV
    </a>
    <button type="button" class="btn btn-dark btn-sm" onclick="window.print()">
        <i class="bi bi-printer me-1"></i>Imprimir reporte
    </button>
@endsection

@push('styles')
    <style>
        .report-kpi .kpi-value { font-size: 1.55rem; }
        .report-print-head {
            display: none;
            align-items: flex-start;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 18px;
            padding-bottom: 14px;
            border-bottom: 2px solid #0f172a;
        }
        .report-print-brand { color: #0f172a; font-size: 1.2rem; font-weight: 800; }
        .report-rank {
            width: 26px;
            height: 26px;
            display: inline-grid;
            place-items: center;
            border: 1px solid #1f2937;
            border-radius: 5px;
            background: #111827;
            color: #fde68a;
            font-family: var(--font-mono);
            font-size: .7rem;
            font-weight: 600;
        }
        .report-alert-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: .65rem 0;
            border-bottom: 1px dashed #e8edf4;
        }
        .report-alert-item:last-child { border-bottom: 0; }

        @media print {
            @page { size: A4 portrait; margin: 12mm; }

            body { background: #fff !important; }
            .sidebar,
            .sidebar-backdrop,
            #sidebarBackdrop,
            .topbar,
            .footer,
            .page-intro,
            .no-print,
            .alert-dismissible { display: none !important; }
            .main-wrapper { margin-left: 0 !important; }
            .content { max-width: none !important; padding: 0 !important; }
            .print-doc-header { display: none !important; }
            .report-print-head { display: flex !important; }
            .card {
                border: 1px solid #e2e8f0 !important;
                box-shadow: none !important;
                break-inside: avoid;
            }
            .table-responsive { overflow: visible !important; }
            .table thead th {
                background: #f8fafc !important;
                color: #334155 !important;
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            .revenue-card,
            .revenue-card *,
            .plate-badge,
            .report-rank,
            .badge {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            .kpi-card::after { display: none; }
            tr { break-inside: avoid; }
        }
    </style>
@endpush

@section('content')
    @php
        $rangos = [
            ['label' => 'Este mes', 'desde' => now()->startOfMonth(), 'hasta' => now()],
            ['label' => 'Mes anterior', 'desde' => now()->subMonthNoOverflow()->startOfMonth(), 'hasta' => now()->subMonthNoOverflow()->endOfMonth()],
            ['label' => 'Últimos 30 días', 'desde' => now()->subDays(29), 'hasta' => now()],
            ['label' => 'Este año', 'desde' => now()->startOfYear(), 'hasta' => now()],
        ];
    @endphp

    <div class="report-print-head">
        <div>
            <div class="report-print-brand">{{ config('taller.nombre') }}</div>
            <div class="small text-secondary">{{ config('taller.direccion') }}</div>
            <div class="small text-secondary">RUC {{ config('taller.ruc') }} · {{ config('taller.telefono') }}</div>
        </div>
        <div class="text-end">
            <div class="fw-bold">Reporte de gestión</div>
            <div class="small">{{ $desde->format('d/m/Y') }} — {{ $hasta->format('d/m/Y') }}</div>
            <div class="small text-secondary">Generado {{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    <div class="card mb-4 no-print">
        <div class="card-body">
            <form method="GET" action="{{ route('reportes.index') }}" class="row g-3 align-items-end">
                <div class="col-sm-6 col-lg-4">
                    <label for="reporte-desde" class="form-label">Fecha inicial</label>
                    <input id="reporte-desde" type="date" name="desde" value="{{ $desde->toDateString() }}" max="{{ now()->toDateString() }}" class="form-control">
                </div>
                <div class="col-sm-6 col-lg-4">
                    <label for="reporte-hasta" class="form-label">Fecha final</label>
                    <input id="reporte-hasta" type="date" name="hasta" value="{{ $hasta->toDateString() }}" max="{{ now()->toDateString() }}" class="form-control">
                </div>
                <div class="col-lg-4 d-flex gap-2">
                    <button type="submit" class="btn btn-dark flex-grow-1">
                        <i class="bi bi-funnel me-1"></i>Aplicar rango
                    </button>
                    <a href="{{ route('reportes.index') }}" class="btn btn-soft" title="Restablecer rango" aria-label="Restablecer rango">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>

            <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
                <span class="small text-secondary fw-bold">Rangos rápidos:</span>
                @foreach ($rangos as $rango)
                    @php($activo = $desde->isSameDay($rango['desde']) && $hasta->isSameDay($rango['hasta']))
                    <a href="{{ route('reportes.index', ['desde' => $rango['desde']->toDateString(), 'hasta' => $rango['hasta']->toDateString()]) }}" class="filter-pill {{ $activo ? 'active' : '' }}">
                        {{ $rango['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h2 class="section-title">Periodo analizado</h2>
            <p class="section-subtitle mb-0">{{ $desde->format('d/m/Y') }} al {{ $hasta->format('d/m/Y') }}</p>
        </div>
        <span class="badge-soft badge-soft-slate">
            <i class="bi bi-calendar-range"></i>{{ $atenciones }} atención(es) registradas
        </span>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card kpi-card report-kpi h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="kpi-icon kpi-green"><i class="bi bi-cash-coin"></i></span>
                        <span class="badge-soft badge-soft-green">Ingresos</span>
                    </div>
                    <div class="kpi-value mt-3">S/ {{ number_format($ingresos, 2) }}</div>
                    <div class="kpi-label">Ingresos del periodo</div>
                    <div class="kpi-meta">{{ $ordenesFacturadas }} orden(es) finalizadas o entregadas</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card kpi-card report-kpi h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="kpi-icon kpi-orange"><i class="bi bi-receipt-cutoff"></i></span>
                        <span class="badge-soft badge-soft-orange">Ticket</span>
                    </div>
                    <div class="kpi-value mt-3">S/ {{ number_format($ticketPromedio, 2) }}</div>
                    <div class="kpi-label">Ticket promedio</div>
                    <div class="kpi-meta">Por orden facturada en el periodo</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card kpi-card report-kpi h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="kpi-icon kpi-blue"><i class="bi bi-clipboard2-check-fill"></i></span>
                        <span class="badge-soft badge-soft-blue">Atenciones</span>
                    </div>
                    <div class="kpi-value mt-3">{{ number_format($atenciones) }}</div>
                    <div class="kpi-label">Atenciones registradas</div>
                    <div class="kpi-meta">Órdenes ingresadas dentro del rango</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card kpi-card report-kpi h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="kpi-icon kpi-purple"><i class="bi bi-box-seam-fill"></i></span>
                        <span class="badge-soft badge-soft-purple">Inventario</span>
                    </div>
                    <div class="kpi-value mt-3">S/ {{ number_format($inventario['valor'], 2) }}</div>
                    <div class="kpi-label">Inventario valorizado</div>
                    <div class="kpi-meta">{{ $inventario['repuestos'] }} repuesto(s) · {{ number_format($inventario['unidades']) }} unidades</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card table-card h-100">
                <div class="card-header">
                    <h2 class="section-title">Top servicios</h2>
                    <p class="section-subtitle">Los más solicitados en órdenes facturadas</p>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Servicio</th>
                                <th scope="col" class="text-end">Cant.</th>
                                <th scope="col" class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($topServicios as $indice => $servicio)
                                <tr>
                                    <td><span class="report-rank">{{ $indice + 1 }}</span></td>
                                    <td class="fw-semibold">{{ $servicio->nombre }}</td>
                                    <td class="text-end mono">{{ (int) $servicio->cantidad }}</td>
                                    <td class="text-end fw-bold text-nowrap money">S/ {{ number_format((float) $servicio->total, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">
                                        <x-empty-state icon="wrench-adjustable" title="Sin servicios facturados" text="No hay órdenes finalizadas o entregadas en el periodo seleccionado." />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card table-card h-100">
                <div class="card-header">
                    <h2 class="section-title">Top repuestos</h2>
                    <p class="section-subtitle">Mayor consumo de inventario en el periodo</p>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Repuesto</th>
                                <th scope="col" class="text-end">Cant.</th>
                                <th scope="col" class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($topRepuestos as $indice => $repuesto)
                                <tr>
                                    <td><span class="report-rank">{{ $indice + 1 }}</span></td>
                                    <td>
                                        <div class="fw-semibold">{{ $repuesto->nombre }}</div>
                                        <div class="small text-secondary data-code">{{ $repuesto->codigo }}</div>
                                    </td>
                                    <td class="text-end mono">{{ (int) $repuesto->cantidad }}</td>
                                    <td class="text-end fw-bold text-nowrap money">S/ {{ number_format((float) $repuesto->total, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">
                                        <x-empty-state icon="box-seam" title="Sin repuestos facturados" text="No hay consumo de repuestos en órdenes facturadas del periodo." />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header">
                    <h2 class="section-title">Inventario valorizado</h2>
                    <p class="section-subtitle">Fotografía actual del almacén</p>
                </div>
                <div class="card-body">
                    <div class="revenue-card card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="revenue-label">Valor total del inventario</span>
                                <i class="bi bi-boxes fs-4 text-warning"></i>
                            </div>
                            <div class="revenue-value">S/ {{ number_format($inventario['valor'], 2) }}</div>
                            <div class="small text-white-50">
                                {{ $inventario['repuestos'] }} repuesto(s) registrados · {{ number_format($inventario['unidades']) }} unidades en stock
                            </div>
                        </div>
                    </div>

                    <div class="row g-2 mt-1">
                        <div class="col-6">
                            <div class="stat-tile h-100">
                                <div class="stat-value">{{ number_format($inventario['unidades']) }}</div>
                                <div class="stat-label">Unidades en stock</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-tile h-100">
                                <div class="stat-value {{ $inventario['bajo'] > 0 ? 'text-danger' : 'text-success' }}">{{ $inventario['bajo'] }}</div>
                                <div class="stat-label">Con stock crítico</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid mt-3 no-print">
                        <a href="{{ route('repuestos.index', $inventario['bajo'] > 0 ? ['stock' => 'bajo'] : []) }}" class="btn btn-soft">
                            <i class="bi bi-box-seam me-1"></i>Ir al inventario
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h2 class="section-title">Alertas de stock crítico</h2>
                        <p class="section-subtitle">Repuestos con 5 unidades o menos</p>
                    </div>
                    @if ($inventario['bajo'] > 0)
                        <span class="badge-soft badge-soft-red">
                            <i class="bi bi-exclamation-triangle"></i>{{ $inventario['bajo'] }} alerta(s)
                        </span>
                    @endif
                </div>
                <div class="card-body">
                    @forelse ($alertas as $repuesto)
                        <div class="report-alert-item">
                            <div class="min-w-0">
                                <div class="stock-name text-truncate">{{ $repuesto->nombre }}</div>
                                <div class="stock-code">{{ $repuesto->codigo }} · {{ $repuesto->marca ?: 'Sin marca' }} · S/ {{ number_format((float) $repuesto->precio_unitario, 2) }}</div>
                            </div>
                            <x-stock-badge :stock="$repuesto->stock" />
                        </div>
                    @empty
                        <div class="text-center py-4 text-secondary">
                            <i class="bi bi-check-circle-fill text-success fs-4 d-block mb-2"></i>
                            <div class="fw-bold text-success">Inventario saludable</div>
                            <div class="small">Ningún repuesto está por debajo del umbral de stock.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <h2 class="section-title">Órdenes del periodo</h2>
                <p class="section-subtitle">Últimas 10 órdenes ingresadas dentro del rango</p>
            </div>
            <a href="{{ route('ordenes.index', ['desde' => $desde->toDateString(), 'hasta' => $hasta->toDateString()]) }}" class="btn btn-soft btn-sm no-print">
                Ver en órdenes <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th scope="col">Orden</th>
                        <th scope="col">Vehículo</th>
                        <th scope="col">Cliente</th>
                        <th scope="col">Ingreso</th>
                        <th scope="col">Estado</th>
                        <th scope="col" class="text-end">Total</th>
                        <th scope="col" class="text-end no-print">Comprobante</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ultimasOrdenes as $orden)
                        <tr>
                            <td><a href="{{ route('ordenes.show', $orden) }}" class="table-link data-code">{{ $orden->codigo }}</a></td>
                            <td>
                                <span class="plate-badge">{{ $orden->vehiculo->placa }}</span>
                                <div class="small text-secondary mt-1">{{ $orden->vehiculo->descripcion }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $orden->vehiculo->cliente->nombre_completo }}</div>
                                <div class="small text-secondary mono">{{ $orden->vehiculo->cliente->documento }}</div>
                            </td>
                            <td>{{ $orden->fecha_ingreso->format('d/m/Y') }}</td>
                            <td><x-estado-badge :estado="$orden->estado" /></td>
                            <td class="text-end fw-bold text-nowrap money">{{ $orden->total_formateado }}</td>
                            <td class="text-end no-print">
                                <a href="{{ route('ordenes.imprimir', $orden) }}" target="_blank" rel="noopener" class="btn btn-outline-dark btn-sm" title="Imprimir comprobante" aria-label="Imprimir comprobante de {{ $orden->codigo }}">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <x-empty-state icon="clipboard2-x" title="Sin órdenes en el periodo" text="No se registraron órdenes dentro del rango de fechas seleccionado.">
                                    <a href="{{ route('ordenes.index') }}" class="btn btn-soft btn-sm">Ver todas las órdenes</a>
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
