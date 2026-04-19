@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ __('brand.meta_description') }}">
    <title>{{ $title ? $title.' | ' : '' }}{{ __('brand.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased text-slate-800 bg-white min-h-screen flex flex-col">
    @include('partials.marketing-header')
    <main class="flex-1 w-full">
        {{ $slot }}
    </main>
    @include('partials.marketing-footer')
    @stack('scripts')
</body>
</html>
