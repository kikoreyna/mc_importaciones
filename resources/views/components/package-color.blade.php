@props(['package'])

@php
    $styles = [
        'azul' => ['#2563eb', 'Recibido en USA'],
        'amarillo' => ['#facc15', 'Recibido en bodega MEX'],
        'verde' => ['#16a34a', 'Documentado'],
    ];
    [$hex, $label] = $styles[$package->color];
@endphp

<span {{ $attributes->merge(['class' => 'd-inline-flex align-items-center']) }} title="{{ $label }}" aria-label="{{ $label }}">
    <span class="d-inline-block h-4 w-4 flex-shrink-0 rounded-pill" style="background-color: {{ $hex }}"></span>
</span>
