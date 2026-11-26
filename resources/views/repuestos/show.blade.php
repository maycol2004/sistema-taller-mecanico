@extends('layouts.app')

@section('title', 'Detalle del repuesto')
@section('eyebrow', 'Inventario básico')
@section('subtitle', 'Información del repuesto, existencias actuales y su participación en órdenes de servicio.')

@section('actions')
    @if (auth()->user()->esAdmin())
        <a href="{{ route('repuestos.edit', $repuesto) }}" class="btn btn-brand btn-sm">
            <i class="bi bi-pencil me-1"></i>Editar repuesto
        </a>
    @endif
    <a href="{{ route('repuestos.index') }}" class="btn btn-soft btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="section-icon section-icon-purple" style="width: 58px; height: 58px; font-size: 1.5rem;"><i class="bi bi-box-seam-fill"></i></span>
                        <div>
                            <h2 class="section-title">{{ $repuesto->nombre }}</h2>
                            <div class="mt-1">
                                <span class="badge-soft badge-soft-slate">{{ $repuesto->codigo }}</span>
                                <span class="text-secondary small ms-1">{{ $repuesto->marca ?: 'Sin marca' }}</span>
                            </div>
                        </div>
                    </div>

                    @if ($repuesto->stock <= \App\Models\Repuesto::STOCK_BAJO)
                        <div class="alert alert-danger small">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            Stock bajo: quedan {{ $repuesto->stock }} unidades. Considera reponer este repuesto.
                        </div>
                    @endif

                    <div class="row g-2 mt-2">
                        <div class="col-6">
                            <div class="stat-tile">
                                <div class="stat-value">S/ {{ number_format((float) $repuesto->precio_unitario, 2) }}</div>
                                <div class="stat-label">Precio unitario</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-tile">
                                <div class="stat-value"><x-stock-badge :stock="$repuesto->stock" /></div>
                                <div class="stat-label">Stock actual</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-tile">
                                <div class="stat-value">{{ $unidadesUsadas }}</div>
                                <div class="stat-label">Unidades usadas</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-tile">
                                <div class="stat-value">S/ {{ number_format($totalGenerado, 2) }}</div>
                                <div class="stat-label">Total generado</div>
                            </div>
                        </div>
                    </div>

                    @if (auth()->user()->esAdmin())
                        <hr class="my-3">
                        <form action="{{ route('repuestos.reponer', $repuesto) }}" method="POST">
                            @csrf
                            <label for="cantidad-reponer" class="form-label">Reposición rápida de stock</label>
                            <div class="input-group">
                                <input id="cantidad-reponer" type="number" name="cantidad" value="5" min="1" max="10000" class="form-control" aria-label="Cantidad a reponer" required>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-plus-lg me-1"></i>Reponer
                                </button>
                            </div>
                            <div class="form-text">Se sumarán las unidades indicadas al stock actual.</div>
                        </form>

                        <div class="d-flex gap-2 mt-3">
                            <a href="{{ route('repuestos.edit', $repuesto) }}" class="btn btn-soft flex-grow-1">
                                <i class="bi bi-pencil me-1"></i>Editar
                            </a>
                            <form action="{{ route('repuestos.destroy', $repuesto) }}" method="POST" onsubmit="return confirm('¿Eliminar este repuesto? Esta acción no se puede deshacer.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" title="Eliminar repuesto" aria-label="Eliminar repuesto">
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
                        <h2 class="section-title">Órdenes que usan este repuesto</h2>
                        <p class="section-subtitle">Trazabilidad del inventario en el taller</p>
                    </div>
                    <a href="{{ route('ordenes.index', ['q' => $repuesto->codigo]) }}" class="btn btn-soft btn-sm">Buscar en órdenes</a>
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
                                    <td class="text-end fw-bold money">S/ {{ number_format((float) $orden->pivot->subtotal, 2) }}</td>
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
                                        <x-empty-state icon="clipboard2-x" title="Aún no se ha usado" text="Este repuesto todavía no forma parte de ninguna orden de servicio.">
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
