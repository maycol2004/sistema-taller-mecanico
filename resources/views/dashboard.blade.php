@extends('layouts.app')

@section('title', 'Panel de control')
@section('eyebrow', 'Resumen operativo')
@section('subtitle', 'Estado actual del taller, carga de trabajo y movimientos recientes.')

@section('actions')
    <a href="{{ route('ordenes.create') }}" class="btn btn-brand btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Nueva orden
    </a>
    <a href="{{ route('clientes.create') }}" class="btn btn-soft btn-sm">
        <i class="bi bi-person-plus me-1"></i>Nuevo cliente
    </a>
    <a href="{{ route('reportes.index') }}" class="btn btn-dark btn-sm">
        <i class="bi bi-bar-chart-line me-1"></i>Reportes
    </a>
@endsection

@section('content')
    @if ($bajosStock > 0)
        <div class="alert alert-warning app-alert stock-alert mb-4">
            <div class="d-flex align-items-center gap-3">
                <span class="stock-alert-icon"><i class="bi bi-exclamation-triangle-fill"></i></span>
                <div>
                    <strong>Reposición requerida</strong>
                    <div class="small">{{ $bajosStock }} repuesto(s) con stock crítico (5 unidades o menos). Revisa el inventario para no detener los trabajos.</div>
                </div>
            </div>
            <a href="{{ route('repuestos.index', ['stock' => 'bajo']) }}" class="btn btn-dark btn-sm">
                <i class="bi bi-box-seam me-1"></i>Revisar inventario
            </a>
        </div>
    @endif

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div class="board-kicker">
            <span class="live-dot" aria-hidden="true"></span>
            Operación en vivo · {{ now()->locale('es')->translatedFormat('d M Y') }}
        </div>
        <span class="badge-soft badge-soft-slate">
            <i class="bi bi-clipboard2-check"></i>{{ number_format($totalOrdenes) }} órdenes totales
        </span>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card kpi-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="kpi-icon kpi-blue"><i class="bi bi-people-fill"></i></span>
                        <span class="badge-soft badge-soft-blue">Cartera</span>
                    </div>
                    <div class="kpi-value mt-3">{{ number_format($clientes) }}</div>
                    <div class="kpi-label">Clientes registrados</div>
                    <div class="kpi-meta">Personas y empresas atendidas</div>
                    <a class="kpi-link" href="{{ route('clientes.index') }}">Ver clientes <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card kpi-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="kpi-icon kpi-green"><i class="bi bi-car-front-fill"></i></span>
                        <span class="badge-soft badge-soft-green">Unidades</span>
                    </div>
                    <div class="kpi-value mt-3">{{ number_format($vehiculos) }}</div>
                    <div class="kpi-label">Vehículos en cartera</div>
                    <div class="kpi-meta">Unidades registradas por cliente</div>
                    <a class="kpi-link" href="{{ route('vehiculos.index') }}">Ver vehículos <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card kpi-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="kpi-icon kpi-purple"><i class="bi bi-box-seam-fill"></i></span>
                        <span class="badge-soft badge-soft-purple">Almacén</span>
                    </div>
                    <div class="kpi-value mt-3">{{ number_format($repuestos) }}</div>
                    <div class="kpi-label">Repuestos registrados</div>
                    <div class="kpi-meta">{{ number_format($unidadesStock) }} unidades en stock</div>
                    <a class="kpi-link" href="{{ route('repuestos.index') }}">Ver inventario <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card kpi-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="kpi-icon kpi-orange"><i class="bi bi-clipboard2-check-fill"></i></span>
                        <span class="badge-soft badge-soft-orange">Taller</span>
                    </div>
                    <div class="kpi-value mt-3">{{ number_format($ordenesActivas) }}</div>
                    <div class="kpi-label">Órdenes activas</div>
                    <div class="kpi-meta">{{ number_format($ordenesMes) }} ingresadas este mes</div>
                    <a class="kpi-link" href="{{ route('ordenes.index') }}">Ver órdenes <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card table-card">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="section-icon"><i class="bi bi-clipboard2-check"></i></span>
                        <div>
                            <h2 class="section-title">Últimas órdenes</h2>
                            <p class="section-subtitle">Ingresos y trabajos recientes del taller</p>
                        </div>
                    </div>
                    <a href="{{ route('ordenes.index') }}" class="btn btn-soft btn-sm">
                        Ver todas <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Orden</th>
                                <th scope="col">Vehículo</th>
                                <th scope="col">Cliente</th>
                                <th scope="col">Estado</th>
                                <th scope="col" class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($ultimasOrdenes as $orden)
                                <tr>
                                    <td>
                                        <a href="{{ route('ordenes.show', $orden) }}" class="table-link data-code">{{ $orden->codigo }}</a>
                                        <div class="text-secondary small mono">{{ $orden->fecha_ingreso->format('d/m/Y') }}</div>
                                    </td>
                                    <td>
                                        <a href="{{ route('vehiculos.show', $orden->vehiculo) }}" class="plate-badge text-decoration-none" title="Ver vehículo {{ $orden->vehiculo->placa }}">
                                            {{ $orden->vehiculo->placa }}
                                        </a>
                                        <div class="small text-secondary mt-1">{{ $orden->vehiculo->descripcion }}</div>
                                    </td>
                                    <td>
                                        <a href="{{ route('clientes.show', $orden->vehiculo->cliente) }}" class="fw-semibold text-decoration-none text-dark">
                                            {{ $orden->vehiculo->cliente->nombre_completo }}
                                        </a>
                                        <div class="small text-secondary mono">{{ $orden->vehiculo->cliente->documento }}</div>
                                    </td>
                                    <td><x-estado-badge :estado="$orden->estado" /></td>
                                    <td class="text-end"><span class="money">{{ $orden->total_formateado }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <x-empty-state icon="clipboard2-x" title="Sin órdenes registradas" text="Crea la primera orden de servicio para empezar a operar el taller.">
                                            <a href="{{ route('ordenes.create') }}" class="btn btn-brand btn-sm">Nueva orden</a>
                                        </x-empty-state>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h2 class="section-title">Carga por estado</h2>
                        <p class="section-subtitle">Distribución del trabajo en piso</p>
                    </div>
                    <span class="badge-soft badge-soft-slate">{{ $totalOrdenes }} en total</span>
                </div>
                <div class="card-body">
                    @foreach (\App\Models\Orden::ESTADOS as $estado)
                        @php
                            $total = (int) ($conteoEstados[$estado] ?? 0);
                            $porcentaje = $totalOrdenes > 0 ? (int) round(($total / $totalOrdenes) * 100) : 0;
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                <x-estado-badge :estado="$estado" />
                                <span class="small fw-semibold text-secondary mono">{{ $total }} · {{ $porcentaje }}%</span>
                            </div>
                            <div class="progress" style="height: 6px" role="progressbar" aria-label="Órdenes {{ $estado }}" aria-valuenow="{{ $porcentaje }}" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar bg-{{ \App\Models\Orden::BADGES_ESTADO[$estado] }}" style="width: {{ $porcentaje }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card revenue-card mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="revenue-label">Facturación del mes</span>
                        <i class="bi bi-cash-coin fs-5 text-warning"></i>
                    </div>
                    <div class="revenue-value">S/ {{ number_format($ingresosMes, 2) }}</div>
                    <div class="small text-white-50">
                        Órdenes finalizadas y entregadas · {{ now()->locale('es')->translatedFormat('F Y') }}
                    </div>
                    <div class="revenue-footer">
                        <span><i class="bi bi-activity me-1"></i>{{ $ordenesActivas }} activas en taller</span>
                        <a href="{{ route('ordenes.index') }}">Ver órdenes <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="section-title">Accesos rápidos</h2>
                    <p class="section-subtitle">Atajos para las tareas frecuentes</p>
                </div>
                <div class="card-body">
                    <div class="quick-grid">
                        <a class="quick-link" href="{{ route('ordenes.create') }}">
                            <i class="bi bi-clipboard2-plus"></i>
                            <span>Nueva orden</span>
                        </a>
                        <a class="quick-link" href="{{ route('clientes.create') }}">
                            <i class="bi bi-person-plus"></i>
                            <span>Nuevo cliente</span>
                        </a>
                        <a class="quick-link" href="{{ route('vehiculos.create') }}">
                            <i class="bi bi-car-front"></i>
                            <span>Nuevo vehículo</span>
                        </a>
                        @if (auth()->user()->esAdmin())
                            <a class="quick-link" href="{{ route('repuestos.create') }}">
                                <i class="bi bi-box-seam"></i>
                                <span>Nuevo repuesto</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h2 class="section-title">Stock crítico</h2>
                        <p class="section-subtitle">5 unidades o menos</p>
                    </div>
                    <a href="{{ route('repuestos.index', ['stock' => 'bajo']) }}" class="btn btn-soft btn-sm">Ver todo</a>
                </div>
                <div class="card-body">
                    @forelse ($repuestosBajoStock as $repuesto)
                        <div class="stock-item flex-wrap">
                            <div class="min-w-0">
                                <div class="stock-name text-truncate">{{ $repuesto->nombre }}</div>
                                <div class="stock-code">{{ $repuesto->codigo }} · {{ $repuesto->marca ?: 'Sin marca' }}</div>
                            </div>
                            <div class="d-flex align-items-center gap-2 ms-auto">
                                <x-stock-badge :stock="$repuesto->stock" />
                                @if (auth()->user()->esAdmin())
                                    <form action="{{ route('repuestos.reponer', $repuesto) }}" method="POST" class="d-flex align-items-center gap-1">
                                        @csrf
                                        <input type="number" name="cantidad" value="5" min="1" max="10000" class="form-control form-control-sm" style="width: 60px;" aria-label="Cantidad a reponer de {{ $repuesto->nombre }}" required>
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Reponer {{ $repuesto->nombre }}" aria-label="Reponer {{ $repuesto->nombre }}">
                                            <i class="bi bi-plus-lg"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-3 text-secondary small">
                            <i class="bi bi-check-circle-fill text-success me-1"></i> Inventario saludable
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
