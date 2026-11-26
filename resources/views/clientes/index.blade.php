@extends('layouts.app')

@section('title', 'Clientes')
@section('eyebrow', 'Módulo de clientes')
@section('subtitle', 'Administra los datos de contacto, documentos y vehículos asociados a cada cliente.')

@section('actions')
    <a href="{{ route('clientes.create') }}" class="btn btn-brand btn-sm">
        <i class="bi bi-person-plus-fill me-1"></i>Nuevo cliente
    </a>
@endsection

@section('content')
    <div class="card table-card">
        <div class="card-header">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h2 class="section-title">Listado de clientes</h2>
                    <p class="section-subtitle">
                        {{ $clientes->total() }} registro(s)
                        @if ($clientes->hasPages())
                            · página {{ $clientes->currentPage() }} de {{ $clientes->lastPage() }}
                        @endif
                    </p>
                </div>
                <form method="GET" action="{{ route('clientes.index') }}" class="search-form" role="search">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="search" name="q" value="{{ $q }}" class="form-control" placeholder="Buscar por DNI, nombre, correo o teléfono…" aria-label="Buscar clientes">
                        @if ($q !== '')
                            <a href="{{ route('clientes.index') }}" class="btn btn-soft" title="Limpiar búsqueda" aria-label="Limpiar búsqueda">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                        <button class="btn btn-dark" type="submit">Buscar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th scope="col">Documento</th>
                        <th scope="col">Cliente</th>
                        <th scope="col">Contacto</th>
                        <th scope="col">Vehículos</th>
                        <th scope="col">Dirección</th>
                        <th scope="col" class="text-end no-print">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clientes as $cliente)
                        <tr>
                            <td>
                                <span class="badge-soft {{ $cliente->tipo_documento === 'RUC' ? 'badge-soft-purple' : 'badge-soft-slate' }}">
                                    {{ $cliente->tipo_documento }}
                                </span>
                                <div class="data-code fw-semibold mt-1">{{ $cliente->documento }}</div>
                            </td>
                            <td>
                                <a href="{{ route('clientes.show', $cliente) }}" class="table-link">{{ $cliente->nombre_completo }}</a>
                                <div class="text-secondary small">{{ $cliente->email ?: 'Sin correo registrado' }}</div>
                            </td>
                            <td>
                                @if ($cliente->telefono)
                                    <a href="tel:{{ $cliente->telefono }}" class="text-decoration-none text-dark fw-semibold">
                                        <i class="bi bi-telephone me-1 text-secondary"></i>{{ $cliente->telefono }}
                                    </a>
                                @else
                                    <span class="text-secondary small">Sin teléfono</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('vehiculos.index', ['cliente_id' => $cliente->id]) }}" class="link-counter">
                                    <i class="bi bi-car-front"></i>{{ $cliente->vehiculos_count }}
                                </a>
                            </td>
                            <td>
                                <span class="text-secondary small">{{ \Illuminate\Support\Str::limit($cliente->direccion ?: 'Sin dirección', 42) }}</span>
                            </td>
                            <td class="no-print">
                                <div class="action-buttons">
                                    <a href="{{ route('clientes.show', $cliente) }}" class="btn btn-outline-secondary" title="Ver detalle" aria-label="Ver cliente {{ $cliente->nombre_completo }}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-outline-primary" title="Editar" aria-label="Editar cliente {{ $cliente->nombre_completo }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if (auth()->user()->esAdmin())
                                        <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" onsubmit="return confirm('¿Eliminar este cliente? Esta acción no se puede deshacer.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Eliminar" aria-label="Eliminar cliente {{ $cliente->nombre_completo }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state icon="people" title="No se encontraron clientes" :text="$q !== '' ? 'No hay coincidencias para «'.$q.'». Prueba con otro término.' : 'Registra tu primer cliente para comenzar a gestionar vehículos y órdenes.'">
                                    @if ($q !== '')
                                        <a href="{{ route('clientes.index') }}" class="btn btn-soft btn-sm">Limpiar búsqueda</a>
                                    @else
                                        <a href="{{ route('clientes.create') }}" class="btn btn-brand btn-sm">Registrar primer cliente</a>
                                    @endif
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($clientes->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $clientes->links() }}
            </div>
        @endif
    </div>
@endsection
