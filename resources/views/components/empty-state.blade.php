@props([
    'icon' => 'inbox',
    'title' => 'Sin registros',
    'text' => 'Aún no hay información para mostrar.',
])

<div {{ $attributes->merge(['class' => 'empty-state']) }}>
    <div class="empty-icon"><i class="bi bi-{{ $icon }}"></i></div>
    <h3>{{ $title }}</h3>
    <p>{{ $text }}</p>
    {{ $slot }}
</div>
