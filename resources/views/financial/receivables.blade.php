<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-display text-2xl md:text-3xl font-bold text-slate-900">{{ __('finance.receivables_title') }}</h2>
            <p class="mt-1 text-sm text-slate-600">{{ __('finance.receivables_subtitle') }}</p>
        </div>
    </x-slot>

    <div class="py-10 md:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-4">
            <div class="rounded-3xl border border-slate-200/90 bg-white shadow-xl overflow-hidden ring-1 ring-slate-900/5">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-600">{{ __('shipments.order_number') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-600">{{ __('customers.field_customer_code') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-600">{{ __('exports.shipments_col_customer') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-bold uppercase text-slate-600">{{ __('exports.shipments_col_cost') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-bold uppercase text-slate-600">{{ __('finance.col_balance') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-bold uppercase text-slate-600">{{ __('finance.col_days_open') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-bold uppercase text-slate-600">{{ __('shipments.actions_column') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($shipments as $shipment)
                                <tr class="hover:bg-brand-50/40">
                                    <td class="px-6 py-3 font-mono font-semibold text-brand-900">{{ $shipment->tracking_number }}</td>
                                    <td class="px-6 py-3 font-mono text-slate-800">{{ $shipment->customer?->customer_code ?? '—' }}</td>
                                    <td class="px-6 py-3 text-slate-800">{{ $shipment->customer?->name ?? '—' }}</td>
                                    <td class="px-6 py-3 text-right">{{ $shipment->cost !== null ? '$'.number_format((float) $shipment->cost, 2, ',', '.') : '—' }}</td>
                                    <td class="px-6 py-3 text-right font-semibold text-amber-800">${{ number_format($shipment->balanceDue(), 2, ',', '.') }}</td>
                                    <td class="px-6 py-3 text-right text-slate-600">{{ now()->diffInDays($shipment->created_at) }}</td>
                                    <td class="px-6 py-3 text-right">
                                        <a href="{{ route('shipments.show', $shipment) }}" class="font-bold text-brand-600 hover:text-brand-800">{{ __('customers.view_detail') }}</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-600">{{ __('shipments.empty') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($shipments->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100">{{ $shipments->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
