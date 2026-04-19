<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-display text-2xl md:text-3xl font-bold text-slate-900 tracking-tight">{{ __('dashboard.title') }}</h2>
            @isset($currentOrganization)
                <p class="mt-1 text-sm text-slate-600">
                    {{ __('dashboard.active_organization') }}
                    <span class="font-semibold text-brand-800">{{ $currentOrganization->name }}</span>
                </p>
            @endisset
        </div>
    </x-slot>

    <div class="py-10 md:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8 px-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="rounded-3xl border border-slate-200/90 bg-white p-6 shadow-xl shadow-slate-900/10 ring-1 ring-slate-900/5">
                    <p class="text-sm font-medium text-slate-500">{{ __('dashboard.metric_registered_today') }}</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $metrics['registered_today'] }}</p>
                </div>
                <div class="rounded-3xl border border-slate-200/90 bg-white p-6 shadow-xl shadow-slate-900/10 ring-1 ring-slate-900/5">
                    <p class="text-sm font-medium text-slate-500">{{ __('dashboard.metric_in_transit') }}</p>
                    <p class="mt-2 text-3xl font-bold text-brand-800">{{ $metrics['in_transit'] }}</p>
                    <p class="mt-1 text-xs text-slate-400">{{ __('dashboard.metric_in_transit_hint') }}</p>
                </div>
                <div class="rounded-3xl border border-slate-200/90 bg-white p-6 shadow-xl shadow-slate-900/10 ring-1 ring-slate-900/5">
                    <p class="text-sm font-medium text-slate-500">{{ __('dashboard.metric_delivered_today') }}</p>
                    <p class="mt-2 text-3xl font-bold text-emerald-700">{{ $metrics['delivered_today'] }}</p>
                </div>
                <div class="rounded-3xl border border-slate-200/90 bg-white p-6 shadow-xl shadow-slate-900/10 ring-1 ring-slate-900/5">
                    <p class="text-sm font-medium text-slate-500">{{ __('dashboard.metric_incidents') }}</p>
                    <p class="mt-2 text-3xl font-bold text-red-700">{{ $metrics['incidents'] }}</p>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200/90 bg-gradient-to-br from-white to-brand-50/40 p-8 shadow-xl shadow-slate-900/10 ring-1 ring-slate-900/5">
                <p class="text-sm text-slate-700">{{ __('dashboard.hint_shipments') }}</p>
                <a href="{{ route('shipments.index') }}" class="mt-4 inline-flex items-center font-semibold text-brand-600 hover:text-brand-800">{{ __('shipments.menu') }} →</a>
            </div>
        </div>
    </div>
</x-app-layout>
