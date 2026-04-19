{{-- Placeholder de marca; puede sustituirse por <img src="{{ asset('images/brand/logo.svg') }}" /> --}}
<div {{ $attributes->class('flex items-center gap-2 shrink-0') }}>
    <span class="sr-only">{{ __('brand.name') }}</span>
    <svg viewBox="0 0 48 48" class="h-10 w-10 shrink-0" aria-hidden="true">
        <defs>
            <linearGradient id="mp-brand-grad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#60a5fa"/>
                <stop offset="100%" stop-color="#2563eb"/>
            </linearGradient>
        </defs>
        <rect width="48" height="48" rx="12" fill="url(#mp-brand-grad)"/>
        <text x="24" y="31" text-anchor="middle" font-family="system-ui, sans-serif" font-size="15" font-weight="700" fill="white">MP</text>
    </svg>
    <span class="hidden sm:inline font-semibold text-slate-900 tracking-tight text-lg leading-none">{{ __('brand.name') }}</span>
</div>
