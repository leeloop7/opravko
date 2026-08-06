@props([
    'sidebar' => false,
])

@if ($sidebar)
    <a
        href="{{ route('plan') }}"
        wire:navigate
        {{ $attributes->except('href')->class('flex items-center gap-2.5 rounded-lg outline-none') }}
    >
        <x-app-logo-icon class="size-7" />
        <x-opravko-wordmark size="xs" />
    </a>
@else
    <a
        href="{{ route('home') }}"
        wire:navigate
        {{ $attributes->except('href')->class('inline-flex items-center outline-none') }}
    >
        <x-opravko-wordmark size="md" />
    </a>
@endif
