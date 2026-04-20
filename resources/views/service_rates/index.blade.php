@php
    use App\Finance\ServiceType;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:justify-between sm:items-start">
            <div>
                <h2 class="font-display text-2xl md:text-3xl font-bold text-slate-900">{{ __('finance.rates_title') }}</h2>
                <p class="mt-2 text-sm text-slate-600">{{ __('finance.rates_subtitle') }}</p>
            </div>
            <a href="{{ route('service-rates.create') }}" class="inline-flex justify-center items-center rounded-2xl bg-brand-600 px-6 py-3 text-base font-bold text-white shadow-xl shadow-brand-600/30 hover:bg-brand-700 shrink-0">{{ __('finance.rate_create') }}</a>
        </div>
    </x-slot>

    <div class="py-10 md:py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 px-4">
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <div class="rounded-3xl border border-slate-200/90 bg-white shadow-xl overflow-hidden ring-1 ring-slate-900/5">
                @if ($rates->isEmpty())
                    <p class="px-6 py-12 text-center text-slate-600">{{ __('finance.rate_empty') }}</p>
                @else
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-600">{{ __('finance.field_service_type') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-bold uppercase text-slate-600">{{ __('finance.field_base_price') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-bold uppercase text-slate-600">{{ __('finance.field_price_per_kg') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-bold uppercase text-slate-600">{{ __('finance.field_price_per_km') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-bold uppercase text-slate-600">{{ __('finance.field_active') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-bold uppercase text-slate-600">{{ __('shipments.actions_column') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($rates as $rate)
                                <tr class="hover:bg-brand-50/40">
                                    <td class="px-6 py-4 font-semibold text-slate-900">{{ ServiceType::label($rate->service_type) }}</td>
                                    <td class="px-6 py-4 text-right">${{ number_format((float) $rate->base_price, 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-right">{{ $rate->price_per_kg !== null ? '$'.number_format((float) $rate->price_per_kg, 4, ',', '.') : '—' }}</td>
                                    <td class="px-6 py-4 text-right">{{ $rate->price_per_km !== null ? '$'.number_format((float) $rate->price_per_km, 4, ',', '.') : '—' }}</td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($rate->active)
                                            <span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-bold text-emerald-900">{{ __('finance.field_active') }}</span>
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('service-rates.edit', $rate) }}" class="font-bold text-brand-600 hover:text-brand-800">{{ __('finance.rate_edit') }}</a>
                                        <form method="POST" action="{{ route('service-rates.destroy', $rate) }}" class="inline" onsubmit="return confirm(@json(__('finance.confirm_delete_rate')));">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-bold text-red-600 hover:text-red-800">{{ __('finance.delete_short') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
