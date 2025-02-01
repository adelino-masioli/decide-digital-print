<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ __('filament-panels::layout.direction') ?? 'ltr' }}"
    @class([
        'fi min-h-screen',
        'dark' => filament()->hasDarkMode() && filament()->hasDarkModeForced(),
    ])
>
    <head>
        {{ \Filament\Support\Facades\FilamentView::renderHook('head.start') }}

        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        {{ \Filament\Support\Facades\FilamentAsset::renderStyles() }}

        @if (! filament()->hasDarkMode())
            <meta name="theme-color" content="#ffffff" />
        @endif

        @stack('scripts')
    </head>

    <body class="min-h-screen antialiased font-normal fi-body">
        <main>
            {{ $slot }}
        </main>

        {{ \Filament\Support\Facades\FilamentAsset::renderScripts() }}

        @stack('scripts')
    </body>
</html> 