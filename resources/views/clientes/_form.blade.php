@php
    $editando = $cliente?->exists ?? false;
@endphp

<form action="{{ $editando ? route('clientes.update', $cliente) : route('clientes.store') }}" method="POST" novalidate>
    @csrf
    @if ($editando)
        @method('PUT')
    @endif

    <div class="row g-3">
        <div class="col-md-5">
            <label for="documento" class="form-label">DNI / RUC <span class="required-mark">*</span></label>
            <input
                type="text"
                id="documento"
                name="documento"
                value="{{ old('documento', $cliente?->documento) }}"
                class="form-control @error('documento') is-invalid @enderror"
                maxlength="20"
                inputmode="numeric"
                autocomplete="off"
                placeholder="Ej. 45678912 o 20547896321"
                required
            >
            @error('documento')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">DNI: 8 dígitos. RUC: 11 dígitos.</div>
        </div>

        <div class="col-md-7">
            <label for="telefono" class="form-label">Teléfono</label>
            <input
                type="tel"
                id="telefono"
                name="telefono"
                value="{{ old('telefono', $cliente?->telefono) }}"
                class="form-control @error('telefono') is-invalid @enderror"
                maxlength="20"
                autocomplete="tel"
                placeholder="Ej. 987 654 321"
            >
            @error('telefono')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="nombres" class="form-label">Nombres <span class="required-mark">*</span></label>
            <input
                type="text"
                id="nombres"
                name="nombres"
                value="{{ old('nombres', $cliente?->nombres) }}"
                class="form-control @error('nombres') is-invalid @enderror"
                maxlength="100"
                autocomplete="given-name"
                placeholder="Nombres o razón social"
                required
            >
            @error('nombres')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="apellidos" class="form-label">Apellidos <span class="required-mark">*</span></label>
            <input
                type="text"
                id="apellidos"
                name="apellidos"
                value="{{ old('apellidos', $cliente?->apellidos) }}"
                class="form-control @error('apellidos') is-invalid @enderror"
                maxlength="100"
                autocomplete="family-name"
                placeholder="Apellidos o S.A.C."
                required
            >
            @error('apellidos')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="email" class="form-label">Correo electrónico</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email', $cliente?->email) }}"
                class="form-control @error('email') is-invalid @enderror"
                maxlength="150"
                autocomplete="email"
                placeholder="cliente@correo.com"
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="direccion" class="form-label">Dirección</label>
            <input
                type="text"
                id="direccion"
                name="direccion"
                value="{{ old('direccion', $cliente?->direccion) }}"
                class="form-control @error('direccion') is-invalid @enderror"
                maxlength="255"
                autocomplete="street-address"
                placeholder="Av. / Jr. / Calle y distrito"
            >
            @error('direccion')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="form-actions">
        <a href="{{ $editando ? route('clientes.show', $cliente) : route('clientes.index') }}" class="btn btn-soft">
            Cancelar
        </a>
        <button type="submit" class="btn btn-brand">
            <i class="bi bi-check2-circle me-1"></i>{{ $editando ? 'Guardar cambios' : 'Registrar cliente' }}
        </button>
    </div>
</form>
