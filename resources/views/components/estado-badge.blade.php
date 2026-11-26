@props(['estado'])

@php
    $clases = [
        'Pendiente' => 'estado-pendiente',
        'En Proceso' => 'estado-en-proceso',
        'Finalizado' => 'estado-finalizado',
        'Entregado' => 'estado-entregado',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'estado-badge '.($clases[$estado] ?? 'badge-soft badge-soft-slate')]) }}>
    <span class="estado-dot" aria-hidden="true"></span>
    {{ $estado }}
</span>
