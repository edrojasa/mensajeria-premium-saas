<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-display text-2xl md:text-3xl font-bold text-slate-900">{{ __('finance.receivables_title') }}</h2>
            <p class="mt-1 text-sm text-slate-600">{{ __('finance.receivables_subtitle') }}</p>
        </div>
    </x-slot>

    <div class="py-10 md:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-4">
            <div class="grid gap-4 md:grid-cols-3 mb-6">
                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                    <p class="text-xs uppercase font-semibold text-slate-500">{{ __('finance.total_general') }}</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">${{ number_format($totalGeneral, 2, ',', '.') }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                    <p class="text-xs uppercase font-semibold text-slate-500">{{ __('finance.total_no_customer') }}</p>
                    <p class="mt-2 text-2xl font-bold text-amber-700">${{ number_format($totalNoCustomer, 2, ',', '.') }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 flex items-end justify-end gap-2">
                    <a href="{{ route('financial.receivables.pdf') }}" class="btn btn-danger inline-flex rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">{{ __('exports.pdf') }}</a>
                    <a href="{{ route('financial.receivables.excel') }}" class="btn btn-success inline-flex rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">{{ __('exports.excel') }}</a>
                </div>
            </div>

            <div class="space-y-4">
                @forelse ($groups as $group)
                    <div class="rounded-2xl border border-slate-200 bg-white p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-bold text-slate-900">{{ $group['customer_name'] }}</h3>
                                @if ($group['customer_code'])
                                    <p class="text-xs text-slate-500">{{ $group['customer_code'] }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-slate-600">{{ $group['shipments_count'] }} envíos</p>
                                <p class="text-lg font-bold text-amber-700">${{ number_format($group['total_due'], 2, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left">{{ __('shipments.order_number') }}</th>
                                        <th class="px-4 py-2 text-right">{{ __('finance.col_balance') }}</th>
                                        <th class="px-4 py-2 text-right">{{ __('finance.col_days_open') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($group['rows'] as $shipment)
                                        <tr>
                                            <td class="px-4 py-2"><a href="{{ route('shipments.show', $shipment) }}" class="font-mono text-brand-700">{{ $shipment->tracking_number }}</a></td>
                                            <td class="px-4 py-2 text-right">${{ number_format($shipment->balanceDue(), 2, ',', '.') }}</td>
                                            <td class="px-4 py-2 text-right">{{ now()->diffInDays($shipment->created_at) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-slate-600">{{ __('shipments.empty') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
