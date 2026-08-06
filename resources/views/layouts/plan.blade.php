<!DOCTYPE html>
<html lang="sl" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="color-scheme" content="dark">
        <meta name="theme-color" content="#101418">
        <title>{{ $title ?? 'Opravko' }} — {{ config('app.name', 'Opravko') }}</title>
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="icon" href="/favicon.ico" sizes="any">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body.plan-body {
                margin: 0;
                min-height: 100vh;
                color: var(--op-text);
                font-family: var(--op-font);
                font-size: 16px;
                line-height: 1.5;
                background:
                    radial-gradient(1000px 520px at 12% -8%, rgba(110, 184, 176, 0.09), transparent 58%),
                    radial-gradient(800px 420px at 92% 0%, rgba(201, 165, 109, 0.05), transparent 50%),
                    linear-gradient(180deg, var(--op-bg) 0%, var(--op-bg-deep) 100%);
                background-attachment: fixed;
            }

            body.plan-body ::selection {
                background: rgba(110, 184, 176, 0.28);
                color: #fff;
            }
        </style>
    </head>
    <body class="plan-body min-h-screen antialiased">
        <div class="op-grain" aria-hidden="true"></div>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
