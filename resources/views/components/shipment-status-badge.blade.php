@props(['status'])

@php
    $classes = match ($status) {
        'received' => 'bg-sky-100 text-sky-900 ring-sky-600/20',
        'in_transit' => 'bg-amber-100 text-amber-950 ring-amber-600/25',
        'out_for_delivery' => 'bg-violet-100 text-violet-900 ring-violet-600/20',
        'delivered' => 'bg-emerald-100 text-emerald-900 ring-emerald-600/20',
        'incident' => 'bg-red-100 text-red-900 ring-red-600/20',
        'cancelled' => 'bg-slate-200 text-slate-800 ring-slate-500/25',
        default => 'bg-slate-100 text-slate-800 ring-slate-500/15',
    };
@endphp

<span {{ $attributes->class('inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 '.$classes) }}>
    {{ __("shipments.status.{$status}") }}
</span>
