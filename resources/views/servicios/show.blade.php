@extends('layouts.app')

@section('title', 'Detalle del servicio')
@section('eyebrow', 'Catálogo de mano de obra')
@section('subtitle', 'Información del servicio, su precio base y las órdenes en las que ha sido utilizado.')

@section('actions')
    @if (auth()->user()->esAdmin())
        <a href="{{ route('servicios.edit', $servicio) }}" class="btn btn-brand btn-sm">
            <i class="bi bi-pencil me-1"></i>Editar servicio
        </a>
    @endif
    <a href="{{ route('servicios.index') }}" class="btn btn-soft btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="section-icon section-icon-blue" style="width: 58px; height: 58px; font-size: 1.5rem;"><i class="bi bi-wrench-adjustable"></i></span>
                        <div>
                            <h2 class="section-title">{{ $servicio->nombre }}</h2>
                            <span class="badge-soft badge-soft-slate mt-1">
                                <i class="bi bi-clock"></i>{{ $servicio->duracion_estimada ?: 'Duración no definida' }}
                            </span>
                        </div>
                    </div>
                    <p class="text-secondary small">{{ $servicio->descripcion ?: 'Este servicio aún no tiene descripción.' }}</p>
                    <div class="row g-2 mt-3">
                        <div class="col-6">
                            <div class="stat-tile">
                                <div class="stat-value">S/ {{ number_format((float) $servicio->precio_base, 2) }}</div>
                                <div class="stat-label">Precio base</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-tile">
                                <div class="stat-value">{{ $vecesUsado }}</div>
                                <div class="stat-label">Veces usado</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="stat-tile">
                                <div class="stat-value">S/ {{ number_format($totalGenerado, 2) }}</div>
                                <div class="stat-label">Total generado en órdenes</div>
                            </div>
                        </div>
                    </div>
                    @if (auth()->user()->esAdmin())
                        <div class="d-flex gap-2 mt-3">
                            <a href="{{ route('servicios.edit', $servicio) }}" class="btn btn-soft flex-grow-1">
                                <i class="bi bi-pencil me-1"></i>Editar
                            </a>
                            <form action="{{ route('servicios.destroy', $servicio) }}" method="POST" onsubmit="return confirm('¿Eliminar este servicio? Esta acción no se puede deshacer.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" title="Eliminar servicio" aria-label="Eliminar servicio">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card table-card">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h2 class="section-title">Órdenes que usan este servicio</h2>
                        <p class="section-subtitle">Historial de asignaciones</p>
                    </div>
                    <a href="{{ route('ordenes.index', ['q' => $servicio->nombre]) }}" class="btn btn-soft btn-sm">Buscar en órdenes</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Orden</th>
                                <th scope="col">Vehículo</th>
                                <th scope="col">Cliente</th>
                                <th scope="col" class="text-end">Cant.</th>
                                <th scope="col" class="text-end">Subtotal</th>
                                <th scope="col">Estado</th>
                                <th scope="col" class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($ordenes as $orden)
                                <tr>
                                    <td><a href="{{ route('ordenes.show', $orden) }}" class="table-link data-code">{{ $orden->codigo }}</a></td>
                                    <td><span class="plate-badge">{{ $orden->vehiculo->placa }}</span></td>
                                    <td>
                                        <div class="fw-semibold">{{ $orden->vehiculo->cliente->nombre_completo }}</div>
                                        <div class="text-secondary small">{{ $orden->fecha_ingreso->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="text-end">{{ $orden->pivot->cantidad }}</td>
                                    <td class="text-end fw-bold text-nowrap money">S/ {{ number_format((float) $orden->pivot->subtotal, 2) }}</td>
                                    <td><x-estado-badge :estado="$orden->estado" /></td>
                                    <td class="text-end">
                                        <a href="{{ route('ordenes.show', $orden) }}" class="btn btn-outline-secondary btn-sm" title="Ver orden">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <x-empty-state icon="clipboard2-x" title="Aún no se ha usado" text="Este servicio todavía no forma parte de ninguna orden de servicio.">
                                            <a href="{{ route('ordenes.create') }}" class="btn btn-brand btn-sm">Crear una orden</a>
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
        </div>
    </div>
@endsection
