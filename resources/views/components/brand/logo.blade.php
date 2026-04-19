@props(['variant' => 'default'])

@php($gradId = 'mp-brand-grad-'.\Illuminate\Support\Str::random(10))

<div {{ $attributes->class('flex items-center gap-1.5 sm:gap-2 shrink-0') }}>
    <span class="sr-only">{{ __('brand.name') }}</span>
    <svg viewBox="0 0 48 48" class="h-8 w-8 shrink-0" aria-hidden="true">
        <defs>
            <linearGradient id="{{ $gradId }}" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#60a5fa"/>
                <stop offset="100%" stop-color="#2563eb"/>
            </linearGradient>
        </defs>
        <rect width="48" height="48" rx="10" fill="url(#{{ $gradId }})"/>
        <text x="24" y="31" text-anchor="middle" font-family="system-ui, sans-serif" font-size="15" font-weight="700" fill="white">MP</text>
    </svg>
    <span @class([
        'hidden sm:inline font-semibold tracking-tight text-sm leading-none',
        'text-slate-900' => $variant === 'default',
        'text-white' => $variant === 'light',
    ])>{{ __('brand.name') }}</span>
</div>
