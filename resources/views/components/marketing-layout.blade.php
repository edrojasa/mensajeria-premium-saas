@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ __('brand.meta_description') }}">
    <title>{{ $title ? $title.' | ' : '' }}{{ __('brand.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:500,600,700,800|figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased text-slate-900 min-h-screen flex flex-col bg-slate-50">
    <div class="print:hidden">
        @include('partials.site-header')
    </div>
    <main class="flex-1 w-full">
        {{ $slot }}
    </main>
    <div class="print:hidden mt-auto">
        @include('partials.site-footer')
    </div>
    @stack('scripts')
</body>
</html>
