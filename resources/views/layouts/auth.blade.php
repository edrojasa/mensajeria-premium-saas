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
    <body class="font-sans antialiased min-h-screen flex flex-col text-slate-900">
        <div class="fixed inset-0 bg-slate-950">
            <div
                class="absolute inset-0 bg-cover bg-center opacity-40"
                style="background-image: url('{{ asset('images/hero/logistics-bg.jpg') }}')"
                aria-hidden="true"
            ></div>
            <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-950/95 to-brand-950/90" aria-hidden="true"></div>
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_rgba(59,130,246,0.18),transparent_55%)]" aria-hidden="true"></div>
        </div>

        <main class="relative z-10 flex flex-1 flex-col items-center justify-center px-4 py-12 sm:py-16">
            <div class="w-full max-w-md">
                {{ $slot }}
            </div>
        </main>

        <p class="relative z-10 pb-6 text-center text-xs text-slate-400">
            © {{ date('Y') }} {{ __('brand.name') }}
        </p>

        @stack('scripts')
    </body>
</html>
