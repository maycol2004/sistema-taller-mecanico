@php
    $editando = $vehiculo?->exists ?? false;
    $clienteSeleccionado = $clienteSeleccionado ?? null;
@endphp

<form action="{{ $editando ? route('vehiculos.update', $vehiculo) : route('vehiculos.store') }}" method="POST" novalidate>
    @csrf
    @if ($editando)
        @method('PUT')
    @endif

    <div class="row g-3">
        <div class="col-12">
            <label for="cliente_id" class="form-label">Cliente propietario <span class="required-mark">*</span></label>
            <select id="cliente_id" name="cliente_id" class="form-select @error('cliente_id') is-invalid @enderror" required>
                <option value="">Seleccione un cliente…</option>
                @foreach ($clientes as $cliente)
                    <option value="{{ $cliente->id }}" @selected((string) old('cliente_id', $vehiculo?->cliente_id ?? $clienteSeleccionado) === (string) $cliente->id)>
                        {{ $cliente->nombre_completo }} · {{ $cliente->tipo_documento }} {{ $cliente->documento }}
                    </option>
                @endforeach
            </select>
            @error('cliente_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-4">
            <label for="placa" class="form-label">Placa <span class="required-mark">*</span></label>
            <input
                type="text"
                id="placa"
                name="placa"
                value="{{ old('placa', $vehiculo?->placa) }}"
                class="form-control text-uppercase @error('placa') is-invalid @enderror"
                maxlength="10"
                autocomplete="off"
                placeholder="ABC-123"
                required
            >
            @error('placa')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">Mayúsculas, sin espacios.</div>
        </div>

        <div class="col-md-4">
            <label for="marca" class="form-label">Marca <span class="required-mark">*</span></label>
            <input
                type="text"
                id="marca"
                name="marca"
                value="{{ old('marca', $vehiculo?->marca) }}"
                class="form-control @error('marca') is-invalid @enderror"
                maxlength="50"
                placeholder="Toyota, Hyundai, Kia…"
                required
            >
            @error('marca')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-4">
            <label for="modelo" class="form-label">Modelo <span class="required-mark">*</span></label>
            <input
                type="text"
                id="modelo"
                name="modelo"
                value="{{ old('modelo', $vehiculo?->modelo) }}"
                class="form-control @error('modelo') is-invalid @enderror"
                maxlength="50"
                placeholder="Corolla, Accent, Sportage…"
                required
            >
            @error('modelo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label for="anio" class="form-label">Año <span class="required-mark">*</span></label>
            <input
                type="number"
                id="anio"
                name="anio"
                value="{{ old('anio', $vehiculo?->anio ?? now()->year) }}"
                class="form-control @error('anio') is-invalid @enderror"
                min="1900"
                max="{{ now()->year + 1 }}"
                placeholder="{{ now()->year }}"
                required
            >
            @error('anio')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label for="color" class="form-label">Color</label>
            <input
                type="text"
                id="color"
                name="color"
                value="{{ old('color', $vehiculo?->color) }}"
                class="form-control @error('color') is-invalid @enderror"
                maxlength="30"
                placeholder="Blanco, negro, gris…"
            >
            @error('color')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="kilometraje" class="form-label">Kilometraje actual <span class="required-mark">*</span></label>
            <div class="input-group">
                <input
                    type="number"
                    id="kilometraje"
                    name="kilometraje"
                    value="{{ old('kilometraje', $vehiculo?->kilometraje ?? 0) }}"
                    class="form-control @error('kilometraje') is-invalid @enderror"
                    min="0"
                    max="2000000"
                    step="1"
                    required
                >
                <span class="input-group-text">km</span>
                @error('kilometraje')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="form-actions">
        <a href="{{ $editando ? route('vehiculos.show', $vehiculo) : route('vehiculos.index') }}" class="btn btn-soft">
            Cancelar
        </a>
        <button type="submit" class="btn btn-brand">
            <i class="bi bi-check2-circle me-1"></i>{{ $editando ? 'Guardar cambios' : 'Registrar vehículo' }}
        </button>
    </div>
</form>
