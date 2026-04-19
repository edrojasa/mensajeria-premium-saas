<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="font-sans antialiased text-slate-900 min-h-screen flex flex-col bg-slate-100">
        <div class="print:hidden">
            @include('partials.site-header')
        </div>

        <main class="flex-1 flex flex-col justify-center px-4 py-10 sm:py-14 bg-gradient-to-br from-slate-100 via-brand-50/40 to-slate-200/60">
            <div class="w-full max-w-md mx-auto">
                {{ $slot }}
            </div>
        </main>

        <div class="print:hidden mt-auto">
            @include('partials.site-footer')
        </div>

        @stack('scripts')
    </body>
</html>
