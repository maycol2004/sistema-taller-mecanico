@extends('layouts.app')

@section('title', 'Detalle del vehículo')
@section('eyebrow', 'Módulo de vehículos')
@section('subtitle', 'Ficha técnica del vehículo, propietario y todas las órdenes de servicio registradas.')

@section('actions')
    <a href="{{ route('ordenes.create', ['vehiculo_id' => $vehiculo->id]) }}" class="btn btn-brand btn-sm">
        <i class="bi bi-clipboard2-plus me-1"></i>Nueva orden
    </a>
    <a href="{{ route('vehiculos.edit', $vehiculo) }}" class="btn btn-soft btn-sm">
        <i class="bi bi-pencil me-1"></i>Editar
    </a>
    <a href="{{ route('vehiculos.index') }}" class="btn btn-soft btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
@endsection

@section('content')
    <div class="card order-header mb-4">
        <div class="card-body">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <div class="d-flex align-items-center gap-3">
                        <span class="kpi-icon kpi-green flex-shrink-0"><i class="bi bi-car-front-fill"></i></span>
                        <div>
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="plate-badge">{{ $vehiculo->placa }}</span>
                                <span class="badge-soft badge-soft-slate">{{ $vehiculo->anio }}</span>
                            </div>
                            <h2 class="order-code mt-2 mb-0">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</h2>
                            <p class="text-secondary mb-0">{{ $vehiculo->color ?: 'Color no registrado' }} · {{ number_format($vehiculo->kilometraje) }} km</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <div class="stat-tile h-100">
                                <div class="stat-value">{{ $totalOrdenes }}</div>
                                <div class="stat-label">Órdenes</div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="stat-tile h-100">
                                <div class="stat-value">{{ number_format($vehiculo->kilometraje) }}</div>
                                <div class="stat-label">Kilometraje</div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="stat-tile h-100">
                                <div class="stat-value">S/ {{ number_format($totalFacturado, 2) }}</div>
                                <div class="stat-label">Facturado</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h2 class="section-title">Propietario</h2>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="profile-avatar" style="width: 54px; height: 54px; border-radius: 15px; font-size: 1rem;">
                            {{ strtoupper(mb_substr($vehiculo->cliente->nombres, 0, 1).mb_substr($vehiculo->cliente->apellidos, 0, 1)) }}
                        </div>
                        <div>
                            <a href="{{ route('clientes.show', $vehiculo->cliente) }}" class="table-link">{{ $vehiculo->cliente->nombre_completo }}</a>
                            <div class="text-secondary small mono">{{ $vehiculo->cliente->tipo_documento }} {{ $vehiculo->cliente->documento }}</div>
                        </div>
                    </div>
                    <dl class="detail-list">
                        <dt>Teléfono</dt>
                        <dd>{{ $vehiculo->cliente->telefono ?: 'No registrado' }}</dd>
                        <dt>Correo</dt>
                        <dd>{{ $vehiculo->cliente->email ?: 'No registrado' }}</dd>
                        <dt>Dirección</dt>
                        <dd>{{ $vehiculo->cliente->direccion ?: 'No registrada' }}</dd>
                    </dl>
                    <a href="{{ route('clientes.show', $vehiculo->cliente) }}" class="btn btn-soft w-100 mt-3">
                        <i class="bi bi-person-vcard me-1"></i>Ver ficha del cliente
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card table-card">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h2 class="section-title">Historial de órdenes</h2>
                        <p class="section-subtitle">Servicios realizados a este vehículo</p>
                    </div>
                    <a href="{{ route('ordenes.create', ['vehiculo_id' => $vehiculo->id]) }}" class="btn btn-brand btn-sm">
                        <i class="bi bi-plus-lg me-1"></i>Nueva orden
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Orden</th>
                                <th scope="col">Ingreso</th>
                                <th scope="col">Entrega estimada</th>
                                <th scope="col">Estado</th>
                                <th scope="col" class="text-end">Total</th>
                                <th scope="col" class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($vehiculo->ordenes as $orden)
                                <tr>
                                    <td><a href="{{ route('ordenes.show', $orden) }}" class="table-link data-code">{{ $orden->codigo }}</a></td>
                                    <td>{{ $orden->fecha_ingreso->format('d/m/Y') }}</td>
                                    <td>{{ $orden->fecha_entrega_real?->format('d/m/Y') ?? $orden->fecha_estimada_entrega?->format('d/m/Y') ?? 'Sin fecha' }}</td>
                                    <td><x-estado-badge :estado="$orden->estado" /></td>
                                    <td class="text-end fw-bold money">{{ $orden->total_formateado }}</td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="{{ route('ordenes.show', $orden) }}" class="btn btn-outline-secondary btn-sm" title="Ver orden" aria-label="Ver orden {{ $orden->codigo }}">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('ordenes.imprimir', $orden) }}" target="_blank" rel="noopener" class="btn btn-outline-dark btn-sm" title="Imprimir comprobante" aria-label="Imprimir comprobante de {{ $orden->codigo }}">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <x-empty-state icon="clipboard2-x" title="Sin órdenes registradas" text="Este vehículo todavía no tiene órdenes de servicio. Crea la primera para comenzar.">
                                            <a href="{{ route('ordenes.create', ['vehiculo_id' => $vehiculo->id]) }}" class="btn btn-brand btn-sm">Nueva orden</a>
                                        </x-empty-state>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if (auth()->user()->esAdmin())
                <div class="d-flex justify-content-end mt-3">
                    <form action="{{ route('vehiculos.destroy', $vehiculo) }}" method="POST" onsubmit="return confirm('¿Eliminar este vehículo? Esta acción no se puede deshacer.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-trash me-1"></i>Eliminar vehículo
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection
