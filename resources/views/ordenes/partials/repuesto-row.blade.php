<tr>
    <td>
        <select
            name="repuestos[{{ $i }}][repuesto_id]"
            class="form-select form-select-sm repuesto-select @error("repuestos.$i.repuesto_id") is-invalid @enderror"
            aria-label="Repuesto"
        >
            <option value="">Seleccione un repuesto…</option>
            @foreach ($repuestos as $repuesto)
                @php($stockDisponible = (int) ($repuesto->stock_disponible ?? $repuesto->stock))
                <option
                    value="{{ $repuesto->id }}"
                    data-precio="{{ $repuesto->precio_unitario }}"
                    data-stock="{{ $stockDisponible }}"
                    @selected((string) ($detalle['repuesto_id'] ?? '') === (string) $repuesto->id)
                >
                    {{ $repuesto->codigo }} · {{ $repuesto->nombre }} · S/ {{ number_format((float) $repuesto->precio_unitario, 2) }} · Stock: {{ $stockDisponible }}
                </option>
            @endforeach
        </select>
        <div class="stock-hint small text-secondary mt-1"></div>
        @error("repuestos.$i.repuesto_id")
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </td>
    <td>
        <input
            type="number"
            name="repuestos[{{ $i }}][cantidad]"
            value="{{ $detalle['cantidad'] ?? 1 }}"
            class="form-control form-control-sm cantidad-input @error("repuestos.$i.cantidad") is-invalid @enderror"
            min="1"
            max="99"
            step="1"
            aria-label="Cantidad del repuesto"
        >
        @error("repuestos.$i.cantidad")
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </td>
    <td class="text-end text-secondary precio-unitario money">S/ 0.00</td>
    <td class="text-end fw-bold subtotal money">S/ 0.00</td>
    <td class="text-end">
        <button type="button" class="btn btn-sm btn-outline-danger remove-row" title="Quitar repuesto" aria-label="Quitar repuesto">
            <i class="bi bi-x-lg"></i>
        </button>
    </td>
</tr>
