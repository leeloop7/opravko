@props([
    'boxed' => true,
])

<img
    src="{{ asset('brand/opravko-mark.svg') }}"
    alt="Opravko"
    {{ $attributes->class(['block']) }}
/>
