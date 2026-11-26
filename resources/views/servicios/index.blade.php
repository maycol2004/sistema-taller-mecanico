@extends('layouts.app')

@section('title', 'Servicios')
@section('eyebrow', 'Catálogo de mano de obra')
@section('subtitle', 'Define los servicios que ofrece el taller, su precio base y el tiempo estimado de atención.')

@section('actions')
    @if (auth()->user()->esAdmin())
        <a href="{{ route('servicios.create') }}" class="btn btn-brand btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Nuevo servicio
        </a>
    @endif
@endsection

@section('content')
    @php($hayFiltros = $q !== '' || filled($precioMin) || filled($precioMax))

    <div class="card table-card">
        <div class="card-header">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h2 class="section-title">Catálogo de servicios</h2>
                    <p class="section-subtitle">
                        {{ $servicios->total() }} servicio(s)
                        @if ($servicios->hasPages())
                            · página {{ $servicios->currentPage() }} de {{ $servicios->lastPage() }}
                        @endif
                    </p>
                </div>
                <form method="GET" action="{{ route('servicios.index') }}" class="search-form search-form-wide" role="search">
                    <div class="row g-2">
                        <div class="col-12 col-md-5">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="search" name="q" value="{{ $q }}" class="form-control" placeholder="Buscar por nombre o descripción…" aria-label="Buscar servicios">
                            </div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div class="input-group">
                                <span class="input-group-text">S/</span>
                                <input type="number" name="precio_min" value="{{ $precioMin }}" min="0" step="0.01" class="form-control" placeholder="Mín." aria-label="Precio mínimo" title="Precio mínimo">
                            </div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div class="input-group">
                                <span class="input-group-text">S/</span>
                                <input type="number" name="precio_max" value="{{ $precioMax }}" min="0" step="0.01" class="form-control" placeholder="Máx." aria-label="Precio máximo" title="Precio máximo">
                            </div>
                        </div>
                        <div class="col-12 col-md-3 d-flex gap-2">
                            <button class="btn btn-dark flex-grow-1" type="submit">
                                <i class="bi bi-funnel me-1"></i>Aplicar filtros
                            </button>
                            @if ($hayFiltros)
                                <a href="{{ route('servicios.index') }}" class="btn btn-soft" title="Limpiar filtros" aria-label="Limpiar filtros">
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
                        <th scope="col">Servicio</th>
                        <th scope="col">Duración</th>
                        <th scope="col" class="text-end">Precio base</th>
                        <th scope="col">Uso en órdenes</th>
                        <th scope="col" class="text-end no-print">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($servicios as $servicio)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="section-icon section-icon-blue flex-shrink-0"><i class="bi bi-wrench-adjustable"></i></span>
                                    <div>
                                        <a href="{{ route('servicios.show', $servicio) }}" class="table-link">{{ $servicio->nombre }}</a>
                                        <div class="text-secondary small">{{ \Illuminate\Support\Str::limit($servicio->descripcion ?: 'Sin descripción', 62) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge-soft badge-soft-slate">
                                    <i class="bi bi-clock"></i>{{ $servicio->duracion_estimada ?: 'No definida' }}
                                </span>
                            </td>
                            <td class="text-end fw-bold text-nowrap money">S/ {{ number_format((float) $servicio->precio_base, 2) }}</td>
                            <td>
                                <a href="{{ route('ordenes.index', ['q' => $servicio->nombre]) }}" class="link-counter">
                                    <i class="bi bi-clipboard2-check"></i>{{ $servicio->ordenes_count }}
                                </a>
                            </td>
                            <td class="no-print">
                                <div class="action-buttons">
                                    <a href="{{ route('servicios.show', $servicio) }}" class="btn btn-outline-secondary" title="Ver detalle" aria-label="Ver servicio {{ $servicio->nombre }}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if (auth()->user()->esAdmin())
                                        <a href="{{ route('servicios.edit', $servicio) }}" class="btn btn-outline-primary" title="Editar" aria-label="Editar servicio {{ $servicio->nombre }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('servicios.destroy', $servicio) }}" method="POST" onsubmit="return confirm('¿Eliminar este servicio? Esta acción no se puede deshacer.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Eliminar" aria-label="Eliminar servicio {{ $servicio->nombre }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-empty-state icon="wrench-adjustable" title="No se encontraron servicios" :text="$hayFiltros ? 'No hay coincidencias con los filtros aplicados.' : 'Crea el primer servicio del catálogo para poder asignarlo a las órdenes.'">
                                    @if ($hayFiltros)
                                        <a href="{{ route('servicios.index') }}" class="btn btn-soft btn-sm">Limpiar filtros</a>
                                    @elseif (auth()->user()->esAdmin())
                                        <a href="{{ route('servicios.create') }}" class="btn btn-brand btn-sm">Crear primer servicio</a>
                                    @endif
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($servicios->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $servicios->links() }}
            </div>
        @endif
    </div>
@endsection
