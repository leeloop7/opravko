@props([
    'size' => 'md',
    'onDark' => false,
])

@php
    $heights = [
        'xs' => 'h-5',
        'sm' => 'h-6',
        'md' => 'h-7',
        'lg' => 'h-8',
        'xl' => 'h-10',
    ];
@endphp

<img
    src="{{ $onDark ? asset('brand/opravko-wordmark-on-dark.svg') : asset('logo.svg') }}"
    alt="Opravko"
    {{ $attributes->class([$heights[$size] ?? $heights['md'], 'w-auto select-none']) }}
/>
