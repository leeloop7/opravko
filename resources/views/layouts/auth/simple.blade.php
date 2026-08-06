<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
        <meta name="color-scheme" content="dark">
        <meta name="theme-color" content="#101418">
        <style>
            body.auth-body {
                margin: 0;
                min-height: 100vh;
                color: var(--op-text);
                font-family: var(--op-font);
                background:
                    radial-gradient(900px 480px at 50% -12%, rgba(110, 184, 176, 0.12), transparent 55%),
                    linear-gradient(180deg, var(--op-bg) 0%, var(--op-bg-deep) 100%);
            }
        </style>
    </head>
    <body class="auth-body antialiased">
        <div class="op-grain" aria-hidden="true"></div>

        <div class="relative z-10 flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-sm flex-col gap-6">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-3 font-medium" wire:navigate>
                    <x-opravko-wordmark size="lg" :on-dark="true" />
                </a>
                <div
                    class="rounded-2xl border p-6"
                    style="border-color: var(--op-line-strong); background: linear-gradient(180deg, rgba(255,255,255,0.04), rgba(255,255,255,0.02));"
                >
                    {{ $slot }}
                </div>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
