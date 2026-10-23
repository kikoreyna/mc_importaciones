@props(['package'])

@php
    $styles = [
        'azul' => ['#2563eb', 'Recibido en USA'],
        'amarillo' => ['#facc15', 'Recibido en bodega MEX'],
        'verde' => ['#16a34a', 'Documentado'],
    ];
    [$hex, $label] = $styles[$package->color];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center']) }} title="{{ $label }}" aria-label="{{ $label }}">
    <span class="inline-block h-4 w-4 shrink-0 rounded-full" style="background-color: {{ $hex }}"></span>
</span>
