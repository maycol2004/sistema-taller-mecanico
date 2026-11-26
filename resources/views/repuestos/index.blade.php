@extends('layouts.app')

@section('title', 'Repuestos')
@section('eyebrow', 'Inventario básico')
@section('subtitle', 'Controla los repuestos del taller, su precio unitario y las existencias disponibles.')

@section('actions')
    @if ($bajosStock > 0)
        <a href="{{ route('repuestos.index', ['stock' => 'bajo']) }}" class="btn btn-soft btn-sm">
            <i class="bi bi-exclamation-triangle me-1 text-danger"></i>{{ $bajosStock }} con stock crítico
        </a>
    @endif
    @if (auth()->user()->esAdmin())
        <a href="{{ route('repuestos.create') }}" class="btn btn-brand btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Nuevo repuesto
        </a>
    @endif
@endsection

@section('content')
    <div class="card table-card">
        <div class="card-header">
            <div class="d-flex flex-wrap align-items-end justify-content-between gap-3">
                <div>
                    <h2 class="section-title">Inventario de repuestos</h2>
                    <p class="section-subtitle">
                        {{ $repuestos->total() }} repuesto(s)
                        @if ($repuestos->hasPages())
                            · página {{ $repuestos->currentPage() }} de {{ $repuestos->lastPage() }}
                        @endif
                    </p>
                </div>
                <form method="GET" action="{{ route('repuestos.index') }}" class="search-form search-form-wide" role="search">
                    <div class="row g-2">
                        <div class="col-12 col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="search" name="q" value="{{ $q }}" class="form-control" placeholder="SKU, nombre o marca…" aria-label="Buscar repuestos">
                                @if ($q !== '' || $stock === 'bajo')
                                    <a href="{{ route('repuestos.index') }}" class="btn btn-soft" title="Limpiar filtros" aria-label="Limpiar filtros">
                                        <i class="bi bi-x-lg"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="col-7 col-md-3">
                            <select name="stock" class="form-select" aria-label="Filtrar por stock">
                                <option value="">Todo el stock</option>
                                <option value="bajo" @selected($stock === 'bajo')>Stock crítico (≤ 5)</option>
                            </select>
                        </div>
                        <div class="col-5 col-md-3">
                            <button class="btn btn-dark w-100" type="submit">
                                <i class="bi bi-funnel me-1"></i>Filtrar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th scope="col">SKU</th>
                        <th scope="col">Repuesto</th>
                        <th scope="col">Stock</th>
                        <th scope="col" class="text-end">Precio unitario</th>
                        <th scope="col">Uso en órdenes</th>
                        <th scope="col" class="text-end no-print">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($repuestos as $repuesto)
                        <tr>
                            <td><span class="badge-soft badge-soft-slate">{{ $repuesto->codigo }}</span></td>
                            <td>
                                <a href="{{ route('repuestos.show', $repuesto) }}" class="table-link">{{ $repuesto->nombre }}</a>
                                <div class="text-secondary small">{{ $repuesto->marca ?: 'Sin marca' }}</div>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap align-items-center gap-1">
                                    <x-stock-badge :stock="$repuesto->stock" />
                                    @if ($repuesto->stock <= \App\Models\Repuesto::STOCK_BAJO && auth()->user()->esAdmin())
                                        <form action="{{ route('repuestos.reponer', $repuesto) }}" method="POST" class="reponer-form input-group input-group-sm">
                                            @csrf
                                            <input type="number" name="cantidad" value="5" min="1" max="10000" class="form-control" aria-label="Cantidad a reponer de {{ $repuesto->nombre }}" title="Cantidad a reponer de {{ $repuesto->nombre }}" required>
                                            <button type="submit" class="btn btn-outline-success" title="Reponer stock de {{ $repuesto->nombre }}" aria-label="Reponer stock de {{ $repuesto->nombre }}">
                                                <i class="bi bi-plus-lg"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                            <td class="text-end fw-bold text-nowrap money">S/ {{ number_format((float) $repuesto->precio_unitario, 2) }}</td>
                            <td>
                                <a href="{{ route('ordenes.index', ['q' => $repuesto->codigo]) }}" class="link-counter">
                                    <i class="bi bi-clipboard2-check"></i>{{ $repuesto->ordenes_count }}
                                </a>
                            </td>
                            <td class="no-print">
                                <div class="action-buttons">
                                    <a href="{{ route('repuestos.show', $repuesto) }}" class="btn btn-outline-secondary" title="Ver detalle" aria-label="Ver repuesto {{ $repuesto->nombre }}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if (auth()->user()->esAdmin())
                                        <a href="{{ route('repuestos.edit', $repuesto) }}" class="btn btn-outline-primary" title="Editar" aria-label="Editar repuesto {{ $repuesto->nombre }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('repuestos.destroy', $repuesto) }}" method="POST" onsubmit="return confirm('¿Eliminar este repuesto? Esta acción no se puede deshacer.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Eliminar" aria-label="Eliminar repuesto {{ $repuesto->nombre }}">
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
                                <x-empty-state icon="box-seam" title="No se encontraron repuestos" :text="$q !== '' || $stock === 'bajo' ? 'No hay coincidencias con los filtros aplicados.' : 'Registra el primer repuesto para empezar a controlar el inventario.'">
                                    @if ($q !== '' || $stock === 'bajo')
                                        <a href="{{ route('repuestos.index') }}" class="btn btn-soft btn-sm">Limpiar filtros</a>
                                    @elseif (auth()->user()->esAdmin())
                                        <a href="{{ route('repuestos.create') }}" class="btn btn-brand btn-sm">Registrar primer repuesto</a>
                                    @endif
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($repuestos->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $repuestos->links() }}
            </div>
        @endif
    </div>
@endsection
