@php
    $editando = $orden?->exists ?? false;

    $serviciosSeleccionados = old('servicios');
    if ($serviciosSeleccionados === null) {
        $serviciosSeleccionados = $editando
            ? $orden->servicios->map(fn ($servicio) => [
                'servicio_id' => $servicio->id,
                'cantidad' => (int) $servicio->pivot->cantidad,
            ])->all()
            : [];
    }

    $repuestosSeleccionados = old('repuestos');
    if ($repuestosSeleccionados === null) {
        $repuestosSeleccionados = $editando
            ? $orden->repuestos->map(fn ($repuesto) => [
                'repuesto_id' => $repuesto->id,
                'cantidad' => (int) $repuesto->pivot->cantidad,
            ])->all()
            : [];
    }

    $siguienteServicio = collect($serviciosSeleccionados)->keys()->map(fn ($clave) => (int) $clave)->max();
    $siguienteServicio = $siguienteServicio === null ? 0 : $siguienteServicio + 1;

    $siguienteRepuesto = collect($repuestosSeleccionados)->keys()->map(fn ($clave) => (int) $clave)->max();
    $siguienteRepuesto = $siguienteRepuesto === null ? 0 : $siguienteRepuesto + 1;

    $subtotalServiciosInicial = collect($serviciosSeleccionados)->sum(function ($detalle) use ($servicios) {
        $servicio = $servicios->firstWhere('id', (int) ($detalle['servicio_id'] ?? 0));

        return $servicio ? (float) $servicio->precio_base * (int) ($detalle['cantidad'] ?? 0) : 0;
    });

    $subtotalRepuestosInicial = collect($repuestosSeleccionados)->sum(function ($detalle) use ($repuestos) {
        $repuesto = $repuestos->firstWhere('id', (int) ($detalle['repuesto_id'] ?? 0));

        return $repuesto ? (float) $repuesto->precio_unitario * (int) ($detalle['cantidad'] ?? 0) : 0;
    });

    $totalInicial = $subtotalServiciosInicial + $subtotalRepuestosInicial;
@endphp

<form action="{{ $editando ? route('ordenes.update', $orden) : route('ordenes.store') }}" method="POST" id="ordenForm">
    @csrf
    @if ($editando)
        @method('PUT')
    @endif

    @if ($vehiculos->isEmpty())
        <div class="alert alert-warning app-alert">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            No hay vehículos registrados. <a href="{{ route('vehiculos.create') }}" class="alert-link">Registra un vehículo</a> antes de crear una orden.
        </div>
    @endif

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card form-card mb-4">
                <div class="card-header d-flex flex-wrap align-items-center gap-2">
                    <span class="section-icon"><i class="bi bi-clipboard2-data"></i></span>
                    <div class="flex-grow-1">
                        <h2 class="section-title">Datos de la orden</h2>
                        <p class="section-subtitle">Vehículo, fechas y diagnóstico inicial</p>
                    </div>
                    @if ($editando)
                        <span class="badge-soft badge-soft-blue">{{ $orden->codigo }}</span>
                    @else
                        <span class="badge-soft badge-soft-slate"><i class="bi bi-magic"></i>Código automático</span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="vehiculo_id" class="form-label">Vehículo a atender <span class="required-mark">*</span></label>
                            <select id="vehiculo_id" name="vehiculo_id" class="form-select @error('vehiculo_id') is-invalid @enderror" required>
                                <option value="">Seleccione el vehículo…</option>
                                @foreach ($vehiculos as $vehiculo)
                                    <option value="{{ $vehiculo->id }}" @selected((string) old('vehiculo_id', $orden?->vehiculo_id) === (string) $vehiculo->id)>
                                        {{ $vehiculo->placa }} · {{ $vehiculo->marca }} {{ $vehiculo->modelo }} — {{ $vehiculo->cliente->nombre_completo }}
                                    </option>
                                @endforeach
                            </select>
                            @error('vehiculo_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="fecha_ingreso" class="form-label">Fecha de ingreso <span class="required-mark">*</span></label>
                            <input
                                type="date"
                                id="fecha_ingreso"
                                name="fecha_ingreso"
                                value="{{ old('fecha_ingreso', $orden?->fecha_ingreso?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                                class="form-control @error('fecha_ingreso') is-invalid @enderror"
                                required
                            >
                            @error('fecha_ingreso')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="fecha_estimada_entrega" class="form-label">Entrega estimada</label>
                            <input
                                type="date"
                                id="fecha_estimada_entrega"
                                name="fecha_estimada_entrega"
                                value="{{ old('fecha_estimada_entrega', $orden?->fecha_estimada_entrega?->format('Y-m-d') ?? now()->addDays(2)->format('Y-m-d')) }}"
                                class="form-control @error('fecha_estimada_entrega') is-invalid @enderror"
                            >
                            @error('fecha_estimada_entrega')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="estado" class="form-label">Estado inicial <span class="required-mark">*</span></label>
                            <select id="estado" name="estado" class="form-select @error('estado') is-invalid @enderror" required>
                                @foreach ($estados as $estado)
                                    <option value="{{ $estado }}" @selected(old('estado', $orden?->estado ?? 'Pendiente') === $estado)>{{ $estado }}</option>
                                @endforeach
                            </select>
                            @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="diagnostico" class="form-label">Diagnóstico</label>
                            <textarea
                                id="diagnostico"
                                name="diagnostico"
                                class="form-control @error('diagnostico') is-invalid @enderror"
                                maxlength="2000"
                                rows="3"
                                placeholder="Describe la falla reportada, la revisión realizada y el diagnóstico técnico…"
                            >{{ old('diagnostico', $orden?->diagnostico) }}</textarea>
                            @error('diagnostico')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <textarea
                                id="observaciones"
                                name="observaciones"
                                class="form-control @error('observaciones') is-invalid @enderror"
                                maxlength="2000"
                                rows="3"
                                placeholder="Indicaciones del cliente, autorizaciones, repuestos pendientes o notas internas…"
                            >{{ old('observaciones', $orden?->observaciones) }}</textarea>
                            @error('observaciones')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card form-card mb-4">
                <div class="card-header d-flex flex-wrap align-items-center gap-2">
                    <span class="section-icon section-icon-blue"><i class="bi bi-wrench-adjustable"></i></span>
                    <div class="flex-grow-1">
                        <h2 class="section-title">Servicios de mano de obra</h2>
                        <p class="section-subtitle">Selecciona los servicios y ajusta la cantidad</p>
                    </div>
                    @if ($servicios->isNotEmpty())
                        <button type="button" class="btn btn-soft btn-sm" id="agregarServicio">
                            <i class="bi bi-plus-lg me-1"></i>Agregar servicio
                        </button>
                    @endif
                </div>
                <div class="card-body p-0">
                    @error('servicios')
                        <div class="alert alert-danger m-3 mb-0">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $message }}
                        </div>
                    @enderror
                    @if ($servicios->isEmpty())
                        <div class="alert alert-warning m-3 mb-0">
                            @if (auth()->user()->esAdmin())
                                No hay servicios en el catálogo. <a href="{{ route('servicios.create') }}" class="alert-link">Crea el primero</a> para asignarlo a la orden.
                            @else
                                No hay servicios en el catálogo. Solicita al administrador que registre los servicios disponibles.
                            @endif
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table item-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">Servicio</th>
                                        <th scope="col" style="width: 110px;">Cantidad</th>
                                        <th scope="col" class="text-end" style="width: 130px;">Precio</th>
                                        <th scope="col" class="text-end" style="width: 130px;">Subtotal</th>
                                        <th scope="col" style="width: 52px;"><span class="visually-hidden">Quitar</span></th>
                                    </tr>
                                </thead>
                                <tbody id="serviciosBody">
                                    @foreach ($serviciosSeleccionados as $i => $detalle)
                                        @include('ordenes.partials.servicio-row', ['i' => $i, 'detalle' => $detalle])
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div id="serviciosVacio" class="empty-items {{ count($serviciosSeleccionados) > 0 ? 'd-none' : '' }}">
                            <i class="bi bi-wrench me-1"></i>Aún no has agregado servicios a esta orden.
                        </div>
                    @endif
                </div>
            </div>

            <div class="card form-card">
                <div class="card-header d-flex flex-wrap align-items-center gap-2">
                    <span class="section-icon section-icon-purple"><i class="bi bi-box-seam-fill"></i></span>
                    <div class="flex-grow-1">
                        <h2 class="section-title">Repuestos utilizados</h2>
                        <p class="section-subtitle">
                            {{ $editando ? 'El stock disponible incluye las unidades ya asignadas a esta orden.' : 'Se valida la disponibilidad del stock al guardar.' }}
                        </p>
                    </div>
                    @if ($repuestos->isNotEmpty())
                        <button type="button" class="btn btn-soft btn-sm" id="agregarRepuesto">
                            <i class="bi bi-plus-lg me-1"></i>Agregar repuesto
                        </button>
                    @endif
                </div>
                <div class="card-body p-0">
                    @error('repuestos')
                        <div class="alert alert-danger m-3 mb-0">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $message }}
                        </div>
                    @enderror
                    @if ($repuestos->isEmpty())
                        <div class="alert alert-warning m-3 mb-0">
                            @if (auth()->user()->esAdmin())
                                No hay repuestos en el inventario. <a href="{{ route('repuestos.create') }}" class="alert-link">Registra el primero</a> para asignarlo a la orden.
                            @else
                                No hay repuestos en el inventario. Solicita al administrador que registre los repuestos disponibles.
                            @endif
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table item-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">Repuesto</th>
                                        <th scope="col" style="width: 110px;">Cantidad</th>
                                        <th scope="col" class="text-end" style="width: 130px;">Precio</th>
                                        <th scope="col" class="text-end" style="width: 130px;">Subtotal</th>
                                        <th scope="col" style="width: 52px;"><span class="visually-hidden">Quitar</span></th>
                                    </tr>
                                </thead>
                                <tbody id="repuestosBody">
                                    @foreach ($repuestosSeleccionados as $i => $detalle)
                                        @include('ordenes.partials.repuesto-row', ['i' => $i, 'detalle' => $detalle])
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div id="repuestosVacio" class="empty-items {{ count($repuestosSeleccionados) > 0 ? 'd-none' : '' }}">
                            <i class="bi bi-box-seam me-1"></i>Aún no has agregado repuestos a esta orden.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card summary-card">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                        <h2 class="section-title mb-0">Resumen de costos</h2>
                        <span class="badge-soft {{ $editando ? 'badge-soft-blue' : 'badge-soft-slate' }}">
                            {{ $editando ? $orden->codigo : 'Nueva orden' }}
                        </span>
                    </div>

                    <div class="summary-row">
                        <span>Servicios</span>
                        <strong id="resumenServicios">S/ {{ number_format($subtotalServiciosInicial, 2) }}</strong>
                    </div>
                    <div class="summary-row">
                        <span>Repuestos</span>
                        <strong id="resumenRepuestos">S/ {{ number_format($subtotalRepuestosInicial, 2) }}</strong>
                    </div>
                    <hr>
                    <div class="summary-total">
                        <span>Total estimado</span>
                        <strong id="resumenTotal">S/ {{ number_format($totalInicial, 2) }}</strong>
                    </div>

                    <p class="form-text mt-3 mb-0">
                        <i class="bi bi-info-circle me-1"></i>Los precios se toman del catálogo vigente al guardar. El servidor recalcula el total.
                    </p>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-brand btn-lg" id="btnGuardarOrden">
                            <i class="bi bi-check2-circle me-1"></i>{{ $editando ? 'Guardar cambios' : 'Registrar orden' }}
                        </button>
                        <a href="{{ $editando ? route('ordenes.show', $orden) : route('ordenes.index') }}" class="btn btn-soft">
                            Cancelar
                        </a>
                    </div>

                    @if ($editando)
                        <div class="alert alert-light border small mt-3 mb-0">
                            <i class="bi bi-lock me-1"></i>El código <strong>{{ $orden->codigo }}</strong> se conserva al editar.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if ($servicios->isNotEmpty())
        <template id="servicioTemplate">
            @include('ordenes.partials.servicio-row', ['i' => '__INDEX__', 'detalle' => []])
        </template>
    @endif

    @if ($repuestos->isNotEmpty())
        <template id="repuestoTemplate">
            @include('ordenes.partials.repuesto-row', ['i' => '__INDEX__', 'detalle' => []])
        </template>
    @endif
</form>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('ordenForm');
            if (!form) return;

            const serviciosBody = document.getElementById('serviciosBody');
            const repuestosBody = document.getElementById('repuestosBody');
            const servicioTemplate = document.getElementById('servicioTemplate');
            const repuestoTemplate = document.getElementById('repuestoTemplate');
            const serviciosVacio = document.getElementById('serviciosVacio');
            const repuestosVacio = document.getElementById('repuestosVacio');
            const resumenServicios = document.getElementById('resumenServicios');
            const resumenRepuestos = document.getElementById('resumenRepuestos');
            const resumenTotal = document.getElementById('resumenTotal');

            let servicioIndex = {{ $siguienteServicio }};
            let repuestoIndex = {{ $siguienteRepuesto }};

            const formatoMoneda = new Intl.NumberFormat('es-PE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });

            const formatear = (valor) => `S/ ${formatoMoneda.format(valor)}`;

            const alternarVacio = (cuerpo, vacio) => {
                if (!cuerpo || !vacio) return;
                vacio.classList.toggle('d-none', cuerpo.querySelectorAll('tr').length > 0);
            };

            const recalcular = () => {
                let totalServicios = 0;

                serviciosBody?.querySelectorAll('tr').forEach((fila) => {
                    const select = fila.querySelector('.servicio-select');
                    const cantidadInput = fila.querySelector('.cantidad-input');
                    const cantidad = Math.max(0, parseInt(cantidadInput?.value || '0', 10) || 0);
                    const precio = parseFloat(select?.selectedOptions?.[0]?.dataset?.precio || '0') || 0;
                    const subtotal = cantidad * precio;

                    fila.querySelector('.precio-unitario').textContent = formatear(precio);
                    fila.querySelector('.subtotal').textContent = formatear(subtotal);

                    totalServicios += subtotal;
                });

                let totalRepuestos = 0;

                repuestosBody?.querySelectorAll('tr').forEach((fila) => {
                    const select = fila.querySelector('.repuesto-select');
                    const cantidadInput = fila.querySelector('.cantidad-input');
                    const cantidad = Math.max(0, parseInt(cantidadInput?.value || '0', 10) || 0);
                    const opcion = select?.selectedOptions?.[0];
                    const precio = parseFloat(opcion?.dataset?.precio || '0') || 0;
                    const stock = parseInt(opcion?.dataset?.stock || '0', 10) || 0;
                    const subtotal = cantidad * precio;
                    const pistaStock = fila.querySelector('.stock-hint');

                    fila.querySelector('.precio-unitario').textContent = formatear(precio);
                    fila.querySelector('.subtotal').textContent = formatear(subtotal);

                    if (pistaStock) {
                        if (select?.value) {
                            pistaStock.textContent = cantidad > stock
                                ? `Stock insuficiente: disponible ${stock}`
                                : `Stock disponible: ${stock}`;
                            pistaStock.classList.toggle('text-danger', cantidad > stock);
                            pistaStock.classList.toggle('text-secondary', cantidad <= stock);
                        } else {
                            pistaStock.textContent = '';
                        }
                    }

                    totalRepuestos += subtotal;
                });

                if (resumenServicios) resumenServicios.textContent = formatear(totalServicios);
                if (resumenRepuestos) resumenRepuestos.textContent = formatear(totalRepuestos);
                if (resumenTotal) resumenTotal.textContent = formatear(totalServicios + totalRepuestos);

                alternarVacio(serviciosBody, serviciosVacio);
                alternarVacio(repuestosBody, repuestosVacio);
            };

            const agregarFila = (cuerpo, template, indice) => {
                if (!cuerpo || !template) return;

                cuerpo.insertAdjacentHTML('beforeend', template.innerHTML.replace(/__INDEX__/g, indice));
                recalcular();
                cuerpo.lastElementChild?.querySelector('select')?.focus();
            };

            document.getElementById('agregarServicio')?.addEventListener('click', () => {
                agregarFila(serviciosBody, servicioTemplate, servicioIndex++);
            });

            document.getElementById('agregarRepuesto')?.addEventListener('click', () => {
                agregarFila(repuestosBody, repuestoTemplate, repuestoIndex++);
            });

            form.addEventListener('input', recalcular);
            form.addEventListener('change', recalcular);

            form.addEventListener('click', (event) => {
                const boton = event.target.closest('.remove-row');
                if (!boton) return;

                boton.closest('tr')?.remove();
                recalcular();
            });

            form.addEventListener('submit', () => {
                const boton = document.getElementById('btnGuardarOrden');
                if (!boton) return;

                window.setTimeout(() => {
                    boton.disabled = true;
                    boton.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando…';
                }, 0);
            });

            recalcular();
        });
    </script>
@endpush
