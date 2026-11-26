@php
    $editando = $servicio?->exists ?? false;
@endphp

<form action="{{ $editando ? route('servicios.update', $servicio) : route('servicios.store') }}" method="POST" novalidate>
    @csrf
    @if ($editando)
        @method('PUT')
    @endif

    <div class="row g-3">
        <div class="col-12">
            <label for="nombre" class="form-label">Nombre del servicio <span class="required-mark">*</span></label>
            <input
                type="text"
                id="nombre"
                name="nombre"
                value="{{ old('nombre', $servicio?->nombre) }}"
                class="form-control @error('nombre') is-invalid @enderror"
                maxlength="120"
                placeholder="Ej. Cambio de aceite y filtro"
                required
            >
            @error('nombre')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea
                id="descripcion"
                name="descripcion"
                class="form-control @error('descripcion') is-invalid @enderror"
                maxlength="1000"
                rows="4"
                placeholder="Detalla el alcance del trabajo, repuestos incluidos o recomendaciones…"
            >{{ old('descripcion', $servicio?->descripcion) }}</textarea>
            @error('descripcion')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="precio_base" class="form-label">Precio base <span class="required-mark">*</span></label>
            <div class="input-group">
                <span class="input-group-text">S/</span>
                <input
                    type="number"
                    id="precio_base"
                    name="precio_base"
                    value="{{ old('precio_base', $servicio?->precio_base) }}"
                    class="form-control @error('precio_base') is-invalid @enderror"
                    min="0"
                    max="999999.99"
                    step="0.01"
                    placeholder="0.00"
                    required
                >
                @error('precio_base')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-text">Mano de obra. Los repuestos se cobran por separado.</div>
        </div>

        <div class="col-md-6">
            <label for="duracion_estimada" class="form-label">Duración estimada</label>
            <input
                type="text"
                id="duracion_estimada"
                name="duracion_estimada"
                value="{{ old('duracion_estimada', $servicio?->duracion_estimada) }}"
                class="form-control @error('duracion_estimada') is-invalid @enderror"
                maxlength="50"
                placeholder="Ej. 1 hora 30 minutos"
            >
            @error('duracion_estimada')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="form-actions">
        <a href="{{ $editando ? route('servicios.show', $servicio) : route('servicios.index') }}" class="btn btn-soft">
            Cancelar
        </a>
        <button type="submit" class="btn btn-brand">
            <i class="bi bi-check2-circle me-1"></i>{{ $editando ? 'Guardar cambios' : 'Crear servicio' }}
        </button>
    </div>
</form>
