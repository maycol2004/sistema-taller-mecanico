@extends('layouts.app')

@section('title', 'Detalle del cliente')
@section('eyebrow', 'Módulo de clientes')
@section('subtitle', 'Ficha completa del cliente, sus vehículos y las órdenes de servicio asociadas.')

@section('actions')
    <a href="{{ route('vehiculos.create', ['cliente_id' => $cliente->id]) }}" class="btn btn-brand btn-sm">
        <i class="bi bi-car-front-fill me-1"></i>Agregar vehículo
    </a>
    <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-soft btn-sm">
        <i class="bi bi-pencil me-1"></i>Editar
    </a>
    <a href="{{ route('clientes.index') }}" class="btn btn-soft btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="profile-avatar mx-auto mb-3">
                        {{ strtoupper(mb_substr($cliente->nombres, 0, 1).mb_substr($cliente->apellidos, 0, 1)) }}
                    </div>
                    <h2 class="section-title">{{ $cliente->nombre_completo }}</h2>
                    <div class="mt-2">
                        <span class="badge-soft {{ $cliente->tipo_documento === 'RUC' ? 'badge-soft-purple' : 'badge-soft-slate' }}">
                            {{ $cliente->tipo_documento }}
                        </span>
                        <span class="data-code fw-semibold ms-1">{{ $cliente->documento }}</span>
                    </div>
                </div>
                <div class="card-body border-top">
                    <dl class="detail-list">
                        <dt>Teléfono</dt>
                        <dd>
                            @if ($cliente->telefono)
                                <a href="tel:{{ $cliente->telefono }}"><i class="bi bi-telephone me-1"></i>{{ $cliente->telefono }}</a>
                            @else
                                <span class="text-secondary">No registrado</span>
                            @endif
                        </dd>
                        <dt>Correo electrónico</dt>
                        <dd>
                            @if ($cliente->email)
                                <a href="mailto:{{ $cliente->email }}"><i class="bi bi-envelope me-1"></i>{{ $cliente->email }}</a>
                            @else
                                <span class="text-secondary">No registrado</span>
                            @endif
                        </dd>
                        <dt>Dirección</dt>
                        <dd>{{ $cliente->direccion ?: 'No registrada' }}</dd>
                        <dt>Cliente desde</dt>
                        <dd>{{ $cliente->created_at->format('d/m/Y') }}</dd>
                    </dl>
                </div>
                <div class="card-footer bg-white border-0 d-flex flex-wrap gap-2 pb-3">
                    <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-soft flex-grow-1">
                        <i class="bi bi-pencil me-1"></i>Editar
                    </a>
                    @if (auth()->user()->esAdmin())
                        <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" onsubmit="return confirm('¿Eliminar este cliente? Esta acción no se puede deshacer.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger" title="Eliminar cliente" aria-label="Eliminar cliente">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="row g-3 mb-4">
                <div class="col-sm-4">
                    <div class="stat-tile h-100">
                        <div class="stat-value">{{ $cliente->vehiculos->count() }}</div>
                        <div class="stat-label">Vehículos</div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="stat-tile h-100">
                        <div class="stat-value">{{ $totalOrdenes }}</div>
                        <div class="stat-label">Órdenes históricas</div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="stat-tile h-100">
                        <div class="stat-value">S/ {{ number_format($totalFacturado, 2) }}</div>
                        <div class="stat-label">Facturado</div>
                    </div>
                </div>
            </div>

            <div class="card table-card mb-4">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h2 class="section-title">Vehículos del cliente</h2>
                        <p class="section-subtitle">Unidades registradas a su nombre</p>
                    </div>
                    <a href="{{ route('vehiculos.create', ['cliente_id' => $cliente->id]) }}" class="btn btn-soft btn-sm">
                        <i class="bi bi-plus-lg me-1"></i>Agregar
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Placa</th>
                                <th scope="col">Vehículo</th>
                                <th scope="col">Kilometraje</th>
                                <th scope="col">Órdenes</th>
                                <th scope="col" class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($cliente->vehiculos as $vehiculo)
                                <tr>
                                    <td>
                                        <a href="{{ route('vehiculos.show', $vehiculo) }}" class="plate-badge text-decoration-none" title="Ver vehículo {{ $vehiculo->placa }}">
                                            {{ $vehiculo->placa }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('vehiculos.show', $vehiculo) }}" class="table-link">{{ $vehiculo->descripcion }}</a>
                                        <div class="text-secondary small">{{ $vehiculo->color ?: 'Color no registrado' }}</div>
                                    </td>
                                    <td>{{ number_format($vehiculo->kilometraje) }} km</td>
                                    <td>
                                        <span class="link-counter"><i class="bi bi-clipboard2-check"></i>{{ $vehiculo->ordenes_count }}</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('vehiculos.show', $vehiculo) }}" class="btn btn-outline-secondary btn-sm" title="Ver vehículo">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <x-empty-state icon="car-front" title="Sin vehículos registrados" text="Este cliente aún no tiene vehículos asociados.">
                                            <a href="{{ route('vehiculos.create', ['cliente_id' => $cliente->id]) }}" class="btn btn-brand btn-sm">Registrar vehículo</a>
                                        </x-empty-state>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card table-card">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h2 class="section-title">Últimas órdenes</h2>
                        <p class="section-subtitle">Servicios recientes de sus vehículos</p>
                    </div>
                    <a href="{{ route('ordenes.index', ['q' => $cliente->documento]) }}" class="btn btn-soft btn-sm">Ver todas</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Orden</th>
                                <th scope="col">Vehículo</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Ingreso</th>
                                <th scope="col" class="text-end">Total</th>
                                <th scope="col" class="text-end">Comprobante</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($ordenes as $orden)
                                <tr>
                                    <td><a href="{{ route('ordenes.show', $orden) }}" class="table-link data-code">{{ $orden->codigo }}</a></td>
                                    <td>
                                        <a href="{{ route('vehiculos.show', $orden->vehiculo) }}" class="plate-badge text-decoration-none" title="Ver vehículo {{ $orden->vehiculo->placa }}">
                                            {{ $orden->vehiculo->placa }}
                                        </a>
                                    </td>
                                    <td><x-estado-badge :estado="$orden->estado" /></td>
                                    <td>{{ $orden->fecha_ingreso->format('d/m/Y') }}</td>
                                    <td class="text-end fw-bold money">{{ $orden->total_formateado }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('ordenes.imprimir', $orden) }}" target="_blank" rel="noopener" class="btn btn-outline-dark btn-sm" title="Imprimir comprobante" aria-label="Imprimir comprobante de {{ $orden->codigo }}">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <x-empty-state icon="clipboard2-x" title="Sin órdenes registradas" text="Las órdenes de los vehículos de este cliente aparecerán aquí." />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
