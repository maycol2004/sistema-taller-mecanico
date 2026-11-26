@extends('layouts.app')

@section('title', 'Órdenes de servicio')
@section('eyebrow', 'Módulo central')
@section('subtitle', 'Controla el ingreso de vehículos, los trabajos asignados, los repuestos y el costo total de reparación.')

@section('actions')
    <a href="{{ route('ordenes.create') }}" class="btn btn-brand btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Nueva orden
    </a>
@endsection

@section('content')
    @php
        $hayFiltros = $q !== '' || $estado !== null || filled($desde) || filled($hasta);
        $filtrosBase = array_filter([
            'q' => $q !== '' ? $q : null,
            'desde' => $desde,
            'hasta' => $hasta,
        ], fn ($valor) => filled($valor));
    @endphp

    <div class="card table-card mb-4">
        <div class="card-body py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                <div class="filter-pills">
                    <a href="{{ route('ordenes.index', $filtrosBase) }}" class="filter-pill {{ $estado === null ? 'active' : '' }}">
                        Todas <span class="count">{{ $totalOrdenes }}</span>
                    </a>
                    @foreach (\App\Models\Orden::ESTADOS as $estadoFiltro)
                        <a href="{{ route('ordenes.index', array_merge($filtrosBase, ['estado' => $estadoFiltro])) }}" class="filter-pill {{ $estado === $estadoFiltro ? 'active' : '' }}">
                            {{ $estadoFiltro }} <span class="count">{{ (int) ($conteos[$estadoFiltro] ?? 0) }}</span>
                        </a>
                    @endforeach
                </div>
                <span class="badge-soft badge-soft-slate">
                    <i class="bi bi-funnel"></i>{{ $totalOrdenes }} resultado(s)
                </span>
            </div>

            <form method="GET" action="{{ route('ordenes.index') }}" role="search">
                @if ($estado)
                    <input type="hidden" name="estado" value="{{ $estado }}">
                @endif

                <div class="row g-2 align-items-end">
                    <div class="col-lg-5">
                        <label for="orden-q" class="form-label">Buscar</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input id="orden-q" type="search" name="q" value="{{ $q }}" class="form-control" placeholder="Código, placa, cliente o diagnóstico…" aria-label="Buscar órdenes">
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <label for="orden-desde" class="form-label">Ingreso desde</label>
                        <input id="orden-desde" type="date" name="desde" value="{{ $desde }}" max="{{ now()->toDateString() }}" class="form-control">
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <label for="orden-hasta" class="form-label">Ingreso hasta</label>
                        <input id="orden-hasta" type="date" name="hasta" value="{{ $hasta }}" max="{{ now()->toDateString() }}" class="form-control">
                    </div>
                    <div class="col-lg-1">
                        <button class="btn btn-dark w-100" type="submit" title="Aplicar filtros" aria-label="Aplicar filtros">
                            <i class="bi bi-funnel-fill"></i>
                        </button>
                    </div>
                </div>

                @if ($hayFiltros)
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3">
                        <span class="small text-secondary">
                            <i class="bi bi-info-circle me-1"></i>Los filtros de búsqueda, estado y fechas se combinan entre sí.
                        </span>
                        <a href="{{ route('ordenes.index') }}" class="btn btn-soft btn-sm">
                            <i class="bi bi-x-lg me-1"></i>Limpiar filtros
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <div class="card table-card">
        <div class="card-header">
            <h2 class="section-title">Listado de órdenes</h2>
            <p class="section-subtitle">
                {{ $ordenes->total() }} orden(es)
                @if ($ordenes->hasPages())
                    · página {{ $ordenes->currentPage() }} de {{ $ordenes->lastPage() }}
                @endif
            </p>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th scope="col">Orden</th>
                        <th scope="col">Vehículo</th>
                        <th scope="col">Cliente</th>
                        <th scope="col">Ingreso</th>
                        <th scope="col">Entrega estimada</th>
                        <th scope="col">Estado</th>
                        <th scope="col" class="text-end">Total</th>
                        <th scope="col" class="text-end no-print">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ordenes as $orden)
                        <tr>
                            <td>
                                <a href="{{ route('ordenes.show', $orden) }}" class="table-link data-code">{{ $orden->codigo }}</a>
                                <div class="text-secondary small text-nowrap">{{ $orden->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td>
                                <a href="{{ route('vehiculos.show', $orden->vehiculo) }}" class="plate-badge text-decoration-none" title="Ver vehículo {{ $orden->vehiculo->placa }}">
                                    {{ $orden->vehiculo->placa }}
                                </a>
                                <div class="text-secondary small mt-1">{{ $orden->vehiculo->descripcion }}</div>
                            </td>
                            <td>
                                <a href="{{ route('clientes.show', $orden->vehiculo->cliente) }}" class="text-decoration-none fw-semibold text-dark">
                                    {{ $orden->vehiculo->cliente->nombre_completo }}
                                </a>
                                <div class="text-secondary small mono">{{ $orden->vehiculo->cliente->documento }}</div>
                            </td>
                            <td>{{ $orden->fecha_ingreso->format('d/m/Y') }}</td>
                            <td>
                                @if ($orden->fecha_entrega_real)
                                    {{ $orden->fecha_entrega_real->format('d/m/Y') }}
                                    <div class="small text-success fw-bold">Entregada</div>
                                @elseif ($orden->fecha_estimada_entrega)
                                    {{ $orden->fecha_estimada_entrega->format('d/m/Y') }}
                                    @if ($orden->fecha_estimada_entrega->isPast() && $orden->esta_activa)
                                        <div class="small text-danger fw-bold">Vencida</div>
                                    @endif
                                @else
                                    <span class="text-secondary small">Sin fecha</span>
                                @endif
                            </td>
                            <td><x-estado-badge :estado="$orden->estado" /></td>
                            <td class="text-end fw-bold text-nowrap money">{{ $orden->total_formateado }}</td>
                            <td class="no-print">
                                <div class="action-buttons">
                                    <a href="{{ route('ordenes.show', $orden) }}" class="btn btn-outline-secondary" title="Ver detalle" aria-label="Ver orden {{ $orden->codigo }}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @php($siguiente = $orden->siguienteEstado())
                                    @if ($siguiente)
                                        <form action="{{ route('ordenes.avanzar', $orden) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-outline-success" title="Avanzar a {{ $siguiente }}" aria-label="Avanzar orden {{ $orden->codigo }} a {{ $siguiente }}">
                                                <i class="bi bi-arrow-right-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('ordenes.imprimir', $orden) }}" target="_blank" rel="noopener" class="btn btn-outline-dark" title="Imprimir comprobante" aria-label="Imprimir comprobante de la orden {{ $orden->codigo }}">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                    <a href="{{ route('ordenes.edit', $orden) }}" class="btn btn-outline-primary" title="Editar" aria-label="Editar orden {{ $orden->codigo }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if (auth()->user()->esAdmin())
                                        <form action="{{ route('ordenes.destroy', $orden) }}" method="POST" onsubmit="return confirm('¿Eliminar esta orden? Se eliminarán también sus servicios y repuestos asignados.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Eliminar" aria-label="Eliminar orden {{ $orden->codigo }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <x-empty-state icon="clipboard2-x" title="No se encontraron órdenes" :text="$hayFiltros ? 'No hay coincidencias con los filtros aplicados.' : 'Registra la primera orden de servicio para comenzar a operar el taller.'">
                                    @if ($hayFiltros)
                                        <a href="{{ route('ordenes.index') }}" class="btn btn-soft btn-sm">Limpiar filtros</a>
                                    @else
                                        <a href="{{ route('ordenes.create') }}" class="btn btn-brand btn-sm">Crear primera orden</a>
                                    @endif
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($ordenes->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $ordenes->links() }}
            </div>
        @endif
    </div>
@endsection
