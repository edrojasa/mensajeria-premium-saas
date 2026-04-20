@props([
    'href',
    'variant' => 'outline',
])

@php
    $base = 'inline-flex min-h-[2.75rem] shrink-0 items-center justify-center gap-2 whitespace-nowrap rounded-xl px-4 py-2.5 text-sm font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2';
    $variants = [
        'primary' => 'border border-emerald-600/80 bg-emerald-700 text-white shadow-md hover:bg-emerald-800',
        'outline' => 'border border-slate-300 bg-white text-slate-800 shadow-sm hover:bg-slate-50',
    ];
    $variantClass = $variants[$variant] ?? $variants['outline'];
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $base.' '.$variantClass]) }}>
    {{ $slot }}
</a>
