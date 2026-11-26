@extends('layouts.app')

@section('title', 'Orden '.$orden->codigo)
@section('eyebrow', 'Módulo central')
@section('subtitle', 'Detalle completo de la orden de servicio, trabajos realizados, repuestos y costos.')

@section('actions')
    <a href="{{ route('ordenes.imprimir', $orden) }}" target="_blank" rel="noopener" class="btn btn-dark btn-sm">
        <i class="bi bi-printer me-1"></i>Comprobante
    </a>
    <a href="{{ route('ordenes.edit', $orden) }}" class="btn btn-brand btn-sm">
        <i class="bi bi-pencil me-1"></i>Editar orden
    </a>
    <a href="{{ route('ordenes.index') }}" class="btn btn-soft btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver al listado
    </a>
@endsection

@section('content')
    @php
        $pasoActual = array_search($orden->estado, \App\Models\Orden::ESTADOS, true);
        $pasoActual = $pasoActual === false ? 0 : (int) $pasoActual;
        $entregaVencida = $orden->fecha_estimada_entrega
            && $orden->fecha_estimada_entrega->isPast()
            && $orden->esta_activa;
    @endphp

    <div class="card order-header mb-4">
        <div class="card-body">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                        <span class="badge-soft badge-soft-slate data-code">{{ $orden->codigo }}</span>
                        <x-estado-badge :estado="$orden->estado" />
                        @if ($entregaVencida)
                            <span class="badge-soft badge-soft-red">
                                <i class="bi bi-clock-history"></i>Entrega vencida
                            </span>
                        @endif
                    </div>
                    <h2 class="order-code mb-1">{{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }}</h2>
                    <div class="d-flex flex-wrap align-items-center gap-2 text-secondary">
                        <a href="{{ route('vehiculos.show', $orden->vehiculo) }}" class="plate-badge text-decoration-none" title="Ver vehículo {{ $orden->vehiculo->placa }}">
                            {{ $orden->vehiculo->placa }}
                        </a>
                        <span class="small">{{ $orden->vehiculo->anio }} · {{ $orden->vehiculo->color ?: 'Sin color' }}</span>
                        <span class="small">·</span>
                        <a href="{{ route('clientes.show', $orden->vehiculo->cliente) }}" class="small fw-semibold text-decoration-none text-dark">
                            {{ $orden->vehiculo->cliente->nombre_completo }}
                        </a>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="stat-tile h-100">
                                <div class="stat-value">{{ $orden->fecha_ingreso->format('d/m/Y') }}</div>
                                <div class="stat-label">Fecha de ingreso</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-tile h-100">
                                <div class="stat-value {{ $entregaVencida ? 'text-danger' : '' }}">
                                    {{ $orden->fecha_entrega_real?->format('d/m/Y') ?? $orden->fecha_estimada_entrega?->format('d/m/Y') ?? 'Sin fecha' }}
                                </div>
                                <div class="stat-label">{{ $orden->fecha_entrega_real ? 'Entrega real' : 'Entrega estimada' }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="stat-tile h-100 d-flex align-items-center justify-content-between gap-2">
                                <div>
                                    <div class="stat-label">Costo total</div>
                                    <div class="stat-value">{{ $orden->total_formateado }}</div>
                                </div>
                                <i class="bi bi-cash-stack fs-4 text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h2 class="section-title">Progreso de la orden</h2>
                        <p class="section-subtitle">Cambia el estado a medida que avanza el trabajo</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="order-stepper mb-4">
                        @foreach (\App\Models\Orden::ESTADOS as $indice => $estadoPaso)
                            <div class="order-step {{ $indice < $pasoActual ? 'done' : '' }} {{ $indice === $pasoActual ? 'current' : '' }}">
                                <div class="order-step-dot">
                                    @if ($indice < $pasoActual)
                                        <i class="bi bi-check-lg"></i>
                                    @else
                                        {{ $indice + 1 }}
                                    @endif
                                </div>
                                {{ $estadoPaso }}
                            </div>
                        @endforeach
                    </div>

                    @if ($siguienteEstado)
                        <form action="{{ route('ordenes.avanzar', $orden) }}" method="POST" class="mb-3">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-arrow-right-circle me-1"></i>Avanzar a «{{ $siguienteEstado }}»
                            </button>
                        </form>
                    @else
                        <div class="alert alert-success d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Orden entregada el {{ $orden->fecha_entrega_real?->format('d/m/Y') ?? '—' }}.</span>
                        </div>
                    @endif

                    <form action="{{ route('ordenes.estado', $orden) }}" method="POST" class="row g-2 align-items-end no-print">
                        @csrf
                        @method('PATCH')
                        <div class="col-sm-8">
                            <label for="estado" class="form-label">Actualizar estado</label>
                            <select name="estado" id="estado" class="form-select" required>
                                @foreach (\App\Models\Orden::ESTADOS as $estadoOpcion)
                                    <option value="{{ $estadoOpcion }}" @selected($orden->estado === $estadoOpcion)>{{ $estadoOpcion }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <button type="submit" class="btn btn-dark w-100">
                                <i class="bi bi-arrow-repeat me-1"></i>Actualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card table-card mb-4">
                <div class="card-header d-flex flex-wrap align-items-center gap-2">
                    <span class="section-icon section-icon-blue"><i class="bi bi-wrench-adjustable"></i></span>
                    <div class="flex-grow-1">
                        <h2 class="section-title">Servicios de mano de obra</h2>
                        <p class="section-subtitle">{{ $orden->servicios->count() }} servicio(s) asignado(s)</p>
                    </div>
                    <span class="badge-soft badge-soft-blue">S/ {{ number_format($subtotalServicios, 2) }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Servicio</th>
                                <th scope="col" class="text-end">Cantidad</th>
                                <th scope="col" class="text-end">Precio unitario</th>
                                <th scope="col" class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orden->servicios as $servicio)
                                <tr>
                                    <td>
                                        <a href="{{ route('servicios.show', $servicio) }}" class="table-link">{{ $servicio->nombre }}</a>
                                        <div class="text-secondary small">{{ $servicio->duracion_estimada ?: 'Duración no definida' }}</div>
                                    </td>
                                    <td class="text-end">{{ $servicio->pivot->cantidad }}</td>
                                    <td class="text-end text-nowrap money">S/ {{ number_format((float) $servicio->pivot->precio_unitario, 2) }}</td>
                                    <td class="text-end fw-bold text-nowrap money">S/ {{ number_format((float) $servicio->pivot->subtotal, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">
                                        <x-empty-state icon="wrench" title="Sin servicios asignados" text="Edita la orden para agregar los trabajos de mano de obra.">
                                            <a href="{{ route('ordenes.edit', $orden) }}" class="btn btn-brand btn-sm">Agregar servicios</a>
                                        </x-empty-state>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card table-card mb-4">
                <div class="card-header d-flex flex-wrap align-items-center gap-2">
                    <span class="section-icon section-icon-purple"><i class="bi bi-box-seam-fill"></i></span>
                    <div class="flex-grow-1">
                        <h2 class="section-title">Repuestos utilizados</h2>
                        <p class="section-subtitle">{{ $orden->repuestos->count() }} repuesto(s) asignado(s)</p>
                    </div>
                    <span class="badge-soft badge-soft-purple">S/ {{ number_format($subtotalRepuestos, 2) }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Repuesto</th>
                                <th scope="col" class="text-end">Cantidad</th>
                                <th scope="col" class="text-end">Precio unitario</th>
                                <th scope="col" class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orden->repuestos as $repuesto)
                                <tr>
                                    <td>
                                        <a href="{{ route('repuestos.show', $repuesto) }}" class="table-link">{{ $repuesto->nombre }}</a>
                                        <div class="text-secondary small">{{ $repuesto->codigo }} · {{ $repuesto->marca ?: 'Sin marca' }}</div>
                                    </td>
                                    <td class="text-end">{{ $repuesto->pivot->cantidad }}</td>
                                    <td class="text-end text-nowrap money">S/ {{ number_format((float) $repuesto->pivot->precio_unitario, 2) }}</td>
                                    <td class="text-end fw-bold text-nowrap money">S/ {{ number_format((float) $repuesto->pivot->subtotal, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">
                                        <x-empty-state icon="box-seam" title="Sin repuestos asignados" text="Edita la orden para agregar los repuestos utilizados en la reparación.">
                                            <a href="{{ route('ordenes.edit', $orden) }}" class="btn btn-brand btn-sm">Agregar repuestos</a>
                                        </x-empty-state>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h2 class="section-title"><i class="bi bi-clipboard2-pulse me-2 text-primary"></i>Diagnóstico</h2>
                        </div>
                        <div class="card-body">
                            <p class="mb-0 small text-secondary">
                                {{ $orden->diagnostico ?: 'No se registró un diagnóstico para esta orden.' }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h2 class="section-title"><i class="bi bi-chat-left-text me-2 text-warning"></i>Observaciones</h2>
                        </div>
                        <div class="card-body">
                            <p class="mb-0 small text-secondary">
                                {{ $orden->observaciones ?: 'No hay observaciones registradas.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card summary-card mb-4">
                <div class="card-body">
                    <h2 class="section-title mb-3">Resumen de costos</h2>
                    <div class="summary-row">
                        <span>Servicios ({{ $orden->servicios->count() }})</span>
                        <strong>S/ {{ number_format($subtotalServicios, 2) }}</strong>
                    </div>
                    <div class="summary-row">
                        <span>Repuestos ({{ $orden->repuestos->count() }})</span>
                        <strong>S/ {{ number_format($subtotalRepuestos, 2) }}</strong>
                    </div>
                    <hr>
                    <div class="summary-total">
                        <span>Total de la orden</span>
                        <strong>{{ $orden->total_formateado }}</strong>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('ordenes.imprimir', $orden) }}" target="_blank" rel="noopener" class="btn btn-dark">
                            <i class="bi bi-printer me-1"></i>Imprimir comprobante
                        </a>
                        <a href="{{ route('ordenes.edit', $orden) }}" class="btn btn-brand">
                            <i class="bi bi-pencil me-1"></i>Editar orden
                        </a>
                        <a href="{{ route('ordenes.index') }}" class="btn btn-soft">
                            <i class="bi bi-list-ul me-1"></i>Ir al listado
                        </a>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="section-title">Vehículo y propietario</h2>
                </div>
                <div class="card-body">
                    <dl class="detail-list">
                        <dt>Placa</dt>
                        <dd><span class="plate-badge">{{ $orden->vehiculo->placa }}</span></dd>
                        <dt>Vehículo</dt>
                        <dd>{{ $orden->vehiculo->descripcion }}</dd>
                        <dt>Kilometraje</dt>
                        <dd>{{ number_format($orden->vehiculo->kilometraje) }} km</dd>
                        <dt>Propietario</dt>
                        <dd><a href="{{ route('clientes.show', $orden->vehiculo->cliente) }}">{{ $orden->vehiculo->cliente->nombre_completo }}</a></dd>
                        <dt>Documento</dt>
                        <dd class="mono">{{ $orden->vehiculo->cliente->tipo_documento }} {{ $orden->vehiculo->cliente->documento }}</dd>
                        <dt>Teléfono</dt>
                        <dd>{{ $orden->vehiculo->cliente->telefono ?: 'No registrado' }}</dd>
                    </dl>
                    <div class="d-grid gap-2 mt-3">
                        <a href="{{ route('vehiculos.show', $orden->vehiculo) }}" class="btn btn-soft btn-sm">
                            <i class="bi bi-car-front me-1"></i>Ver vehículo
                        </a>
                        <a href="{{ route('clientes.show', $orden->vehiculo->cliente) }}" class="btn btn-soft btn-sm">
                            <i class="bi bi-person-vcard me-1"></i>Ver cliente
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="section-title">Información del registro</h2>
                </div>
                <div class="card-body">
                    <dl class="detail-list">
                        <dt>Código</dt>
                        <dd class="data-code">{{ $orden->codigo }}</dd>
                        <dt>Registrada por</dt>
                        <dd>{{ $orden->usuario?->name ?? 'Usuario no disponible' }}</dd>
                        <dt>Fecha real de entrega</dt>
                        <dd>{{ $orden->fecha_entrega_real?->format('d/m/Y') ?? 'Pendiente' }}</dd>
                        <dt>Creada</dt>
                        <dd>{{ $orden->created_at->format('d/m/Y H:i') }}</dd>
                        <dt>Última actualización</dt>
                        <dd>{{ $orden->updated_at->format('d/m/Y H:i') }}</dd>
                    </dl>
                    @if (auth()->user()->esAdmin())
                        <form action="{{ route('ordenes.destroy', $orden) }}" method="POST" class="mt-3" onsubmit="return confirm('¿Eliminar esta orden? Se eliminarán también sus servicios y repuestos asignados.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-trash me-1"></i>Eliminar orden
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
