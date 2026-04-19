<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:500,600,700,800|figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="font-sans antialiased text-slate-900 min-h-screen flex flex-col bg-gradient-to-br from-slate-100 via-white to-brand-50/40">
        <div class="print:hidden">
            @include('partials.site-header')
        </div>

        @isset($header)
            <header class="relative overflow-hidden border-b border-slate-200/80 bg-gradient-to-r from-white via-brand-50/35 to-white shadow-md shadow-slate-900/5">
                <div class="absolute inset-y-0 right-0 w-1/3 bg-gradient-to-l from-brand-100/50 to-transparent pointer-events-none" aria-hidden="true"></div>
                <div class="relative max-w-7xl mx-auto py-7 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main class="flex-1 w-full">
            {{ $slot }}
        </main>

        <div class="print:hidden mt-auto">
            @include('partials.site-footer')
        </div>

        @stack('scripts')
    </body>
</html>
