<tr>
    <td>
        <select
            name="servicios[{{ $i }}][servicio_id]"
            class="form-select form-select-sm servicio-select @error("servicios.$i.servicio_id") is-invalid @enderror"
            aria-label="Servicio"
        >
            <option value="">Seleccione un servicio…</option>
            @foreach ($servicios as $servicio)
                <option
                    value="{{ $servicio->id }}"
                    data-precio="{{ $servicio->precio_base }}"
                    @selected((string) ($detalle['servicio_id'] ?? '') === (string) $servicio->id)
                >
                    {{ $servicio->nombre }} · S/ {{ number_format((float) $servicio->precio_base, 2) }}
                </option>
            @endforeach
        </select>
        @error("servicios.$i.servicio_id")
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </td>
    <td>
        <input
            type="number"
            name="servicios[{{ $i }}][cantidad]"
            value="{{ $detalle['cantidad'] ?? 1 }}"
            class="form-control form-control-sm cantidad-input @error("servicios.$i.cantidad") is-invalid @enderror"
            min="1"
            max="99"
            step="1"
            aria-label="Cantidad del servicio"
        >
        @error("servicios.$i.cantidad")
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </td>
    <td class="text-end text-secondary precio-unitario money">S/ 0.00</td>
    <td class="text-end fw-bold subtotal money">S/ 0.00</td>
    <td class="text-end">
        <button type="button" class="btn btn-sm btn-outline-danger remove-row" title="Quitar servicio" aria-label="Quitar servicio">
            <i class="bi bi-x-lg"></i>
        </button>
    </td>
</tr>
