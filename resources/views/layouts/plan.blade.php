<!DOCTYPE html>
<html lang="sl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="color-scheme" content="light">
        <title>{{ $title ?? 'Hišni plan 2026' }} — {{ config('app.name', 'Opravko') }}</title>
        <link rel="icon" href="/favicon.ico" sizes="any">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @fluxAppearance
        <style>
            :root {
                --ink: #17324d;
                --text: #26343f;
                --muted: #70808e;
                --teal: #2a7f7a;
                --gold: #a66f2b;
                --red: #b54b4b;
                --line: #dfe6ea;
                --panel: #ffffff;
                --soft: #f5f8f9;
                --done: #e8f5ef;
            }

            body.plan-body {
                margin: 0;
                background: #edf2f3;
                color: var(--text);
                font: 16px/1.45 ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            }
        </style>
    </head>
    <body class="plan-body min-h-screen">
        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
