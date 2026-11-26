@props(['stock'])

@php
    $critico = $stock <= \App\Models\Repuesto::STOCK_BAJO;
    $clase = $critico ? 'stock-critico' : ($stock <= 10 ? 'stock-bajo' : 'stock-ok');
    $etiqueta = $critico ? 'Stock crítico' : ($stock <= 10 ? 'Stock bajo' : 'Stock disponible');
@endphp

<span
    {{ $attributes->merge(['class' => 'stock-badge '.$clase]) }}
    aria-label="{{ $etiqueta }}: {{ $stock }} unidades"
    title="{{ $etiqueta }}: {{ $stock }} unidades"
>
    <span class="estado-dot" aria-hidden="true"></span>
    {{ $stock }} u.
</span>
