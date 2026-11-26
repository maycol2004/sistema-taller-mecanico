@php
    $editando = $repuesto?->exists ?? false;
@endphp

<form action="{{ $editando ? route('repuestos.update', $repuesto) : route('repuestos.store') }}" method="POST" novalidate>
    @csrf
    @if ($editando)
        @method('PUT')
    @endif

    <div class="row g-3">
        <div class="col-md-4">
            <label for="codigo" class="form-label">Código / SKU <span class="required-mark">*</span></label>
            <input
                type="text"
                id="codigo"
                name="codigo"
                value="{{ old('codigo', $repuesto?->codigo) }}"
                class="form-control text-uppercase @error('codigo') is-invalid @enderror"
                maxlength="30"
                placeholder="REP-001"
                required
            >
            @error('codigo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">Solo letras, números y guiones.</div>
        </div>

        <div class="col-md-8">
            <label for="nombre" class="form-label">Nombre del repuesto <span class="required-mark">*</span></label>
            <input
                type="text"
                id="nombre"
                name="nombre"
                value="{{ old('nombre', $repuesto?->nombre) }}"
                class="form-control @error('nombre') is-invalid @enderror"
                maxlength="120"
                placeholder="Ej. Filtro de aceite"
                required
            >
            @error('nombre')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-4">
            <label for="marca" class="form-label">Marca</label>
            <input
                type="text"
                id="marca"
                name="marca"
                value="{{ old('marca', $repuesto?->marca) }}"
                class="form-control @error('marca') is-invalid @enderror"
                maxlength="60"
                placeholder="Bosch, NGK, Mobil…"
            >
            @error('marca')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-4">
            <label for="precio_unitario" class="form-label">Precio unitario <span class="required-mark">*</span></label>
            <div class="input-group">
                <span class="input-group-text">S/</span>
                <input
                    type="number"
                    id="precio_unitario"
                    name="precio_unitario"
                    value="{{ old('precio_unitario', $repuesto?->precio_unitario) }}"
                    class="form-control @error('precio_unitario') is-invalid @enderror"
                    min="0"
                    max="999999.99"
                    step="0.01"
                    placeholder="0.00"
                    required
                >
                @error('precio_unitario')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-4">
            <label for="stock" class="form-label">Stock disponible <span class="required-mark">*</span></label>
            <div class="input-group">
                <input
                    type="number"
                    id="stock"
                    name="stock"
                    value="{{ old('stock', $repuesto?->stock ?? 0) }}"
                    class="form-control @error('stock') is-invalid @enderror"
                    min="0"
                    max="1000000"
                    step="1"
                    required
                >
                <span class="input-group-text">u.</span>
                @error('stock')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="form-actions">
        <a href="{{ $editando ? route('repuestos.show', $repuesto) : route('repuestos.index') }}" class="btn btn-soft">
            Cancelar
        </a>
        <button type="submit" class="btn btn-brand">
            <i class="bi bi-check2-circle me-1"></i>{{ $editando ? 'Guardar cambios' : 'Registrar repuesto' }}
        </button>
    </div>
</form>
