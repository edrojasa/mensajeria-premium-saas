@props(['variant' => 'default', 'size' => 'default'])

@php
    $showText = true;
    $imgClass = ($size ?? 'default') === 'nav'
        ? 'h-10 w-auto md:h-11 max-w-[11rem] md:max-w-[12rem]'
        : 'h-8 w-auto max-w-[10rem]';
    $textClass = ($size ?? 'default') === 'nav'
        ? 'text-base md:text-[1.05rem]'
        : 'text-sm';
@endphp

<div {{ $attributes->class('group flex items-center gap-2 sm:gap-2.5 shrink-0') }}>
    <span class="sr-only">{{ __('brand.name') }}</span>
    @if (brand_logo_asset())
        <img src="{{ brand_logo_asset() }}" alt="{{ __('brand.name') }}" class="{{ $imgClass }} object-contain shrink-0 transition duration-300 ease-out group-hover:scale-105 group-hover:drop-shadow-[0_4px_18px_rgba(37,99,235,0.45)]" />
    @else
        @php($gradId = 'mp-brand-grad-'.\Illuminate\Support\Str::random(10))
        <svg viewBox="0 0 48 48" class="{{ ($size ?? 'default') === 'nav' ? 'h-10 w-10 md:h-11 md:w-11' : 'h-8 w-8' }} shrink-0" aria-hidden="true">
            <defs>
                <linearGradient id="{{ $gradId }}" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#60a5fa"/>
                    <stop offset="100%" stop-color="#2563eb"/>
                </linearGradient>
            </defs>
            <rect width="48" height="48" rx="10" fill="url(#{{ $gradId }})"/>
            <text x="24" y="31" text-anchor="middle" font-family="system-ui, sans-serif" font-size="15" font-weight="700" fill="white">MP</text>
        </svg>
    @endif
    @if ($showText)
        <span @class([
            'hidden sm:inline font-semibold tracking-tight leading-none',
            $textClass,
            'text-slate-900' => $variant === 'default',
            'text-white' => $variant === 'light',
        ])>{{ __('brand.name') }}</span>
    @endif
</div>
