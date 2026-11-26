@extends('layouts.app')

@section('title', 'Vehículos')
@section('eyebrow', 'Módulo de vehículos')
@section('subtitle', 'Consulta y administra los vehículos atendidos en el taller, su kilometraje y su propietario.')

@section('actions')
    <a href="{{ route('vehiculos.create') }}" class="btn btn-brand btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Nuevo vehículo
    </a>
@endsection

@section('content')
    <div class="card table-card">
        <div class="card-header">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h2 class="section-title">Listado de vehículos</h2>
                    <p class="section-subtitle">
                        {{ $vehiculos->total() }} registro(s)
                        @if ($vehiculos->hasPages())
                            · página {{ $vehiculos->currentPage() }} de {{ $vehiculos->lastPage() }}
                        @endif
                    </p>
                </div>
                <form method="GET" action="{{ route('vehiculos.index') }}" class="search-form search-form-wide" role="search">
                    <div class="row g-2">
                        <div class="col-12 col-md-5">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="search" name="q" value="{{ $q }}" class="form-control" placeholder="Placa, marca, modelo o cliente…" aria-label="Buscar vehículos">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <select name="cliente_id" class="form-select" aria-label="Filtrar por cliente">
                                <option value="">Todos los clientes</option>
                                @foreach ($clientes as $cliente)
                                    <option value="{{ $cliente->id }}" @selected((string) $clienteId === (string) $cliente->id)>
                                        {{ $cliente->nombre_completo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-3 d-flex gap-2">
                            <button class="btn btn-dark flex-grow-1" type="submit">
                                <i class="bi bi-funnel me-1"></i>Filtrar
                            </button>
                            @if ($q !== '' || $clienteId)
                                <a href="{{ route('vehiculos.index') }}" class="btn btn-soft" title="Limpiar filtros" aria-label="Limpiar filtros">
                                    <i class="bi bi-x-lg"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th scope="col">Placa</th>
                        <th scope="col">Vehículo</th>
                        <th scope="col">Propietario</th>
                        <th scope="col">Año / Color</th>
                        <th scope="col">Kilometraje</th>
                        <th scope="col">Órdenes</th>
                        <th scope="col" class="text-end no-print">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($vehiculos as $vehiculo)
                        <tr>
                            <td><span class="plate-badge">{{ $vehiculo->placa }}</span></td>
                            <td>
                                <a href="{{ route('vehiculos.show', $vehiculo) }}" class="table-link">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</a>
                                <div class="text-secondary small">{{ $vehiculo->descripcion }}</div>
                            </td>
                            <td>
                                <a href="{{ route('clientes.show', $vehiculo->cliente) }}" class="text-decoration-none fw-semibold text-dark">
                                    {{ $vehiculo->cliente->nombre_completo }}
                                </a>
                                <div class="text-secondary small mono">{{ $vehiculo->cliente->documento }}</div>
                            </td>
                            <td>
                                <span class="badge-soft badge-soft-slate">{{ $vehiculo->anio }}</span>
                                <div class="text-secondary small mt-1">{{ $vehiculo->color ?: 'Sin color' }}</div>
                            </td>
                            <td class="text-nowrap">
                                <span class="mono fw-semibold">{{ number_format($vehiculo->kilometraje) }}</span>
                                <span class="text-secondary small">km</span>
                            </td>
                            <td>
                                <a href="{{ route('ordenes.index', ['q' => $vehiculo->placa]) }}" class="link-counter">
                                    <i class="bi bi-clipboard2-check"></i>{{ $vehiculo->ordenes_count }}
                                </a>
                            </td>
                            <td class="no-print">
                                <div class="action-buttons">
                                    <a href="{{ route('vehiculos.show', $vehiculo) }}" class="btn btn-outline-secondary" title="Ver detalle" aria-label="Ver vehículo {{ $vehiculo->placa }}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('vehiculos.edit', $vehiculo) }}" class="btn btn-outline-primary" title="Editar" aria-label="Editar vehículo {{ $vehiculo->placa }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if (auth()->user()->esAdmin())
                                        <form action="{{ route('vehiculos.destroy', $vehiculo) }}" method="POST" onsubmit="return confirm('¿Eliminar este vehículo? Esta acción no se puede deshacer.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Eliminar" aria-label="Eliminar vehículo {{ $vehiculo->placa }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <x-empty-state icon="car-front" title="No se encontraron vehículos" :text="$q !== '' || $clienteId ? 'No hay coincidencias con los filtros aplicados.' : 'Registra el primer vehículo para comenzar a generar órdenes de servicio.'">
                                    @if ($q !== '' || $clienteId)
                                        <a href="{{ route('vehiculos.index') }}" class="btn btn-soft btn-sm">Limpiar filtros</a>
                                    @else
                                        <a href="{{ route('vehiculos.create') }}" class="btn btn-brand btn-sm">Registrar primer vehículo</a>
                                    @endif
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($vehiculos->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $vehiculos->links() }}
            </div>
        @endif
    </div>
@endsection
