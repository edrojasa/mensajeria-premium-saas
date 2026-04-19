<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-display text-2xl md:text-3xl font-bold text-slate-900">{{ __('shipments.my_shipments_menu') }}</h2>
            <p class="mt-2 text-sm text-slate-600">{{ __('courier.index_subtitle') }}</p>
        </div>
    </x-slot>

    <div class="py-10 md:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-4">
            <div class="rounded-3xl border border-slate-200/90 bg-white shadow-2xl shadow-slate-900/10 overflow-hidden ring-1 ring-slate-900/5">
                <div class="p-5 sm:p-8">
                    @if ($shipments->isEmpty())
                        <div class="text-center py-14 text-slate-600">{{ __('courier.empty') }}</div>
                    @else
                        <div class="overflow-x-auto rounded-2xl border border-slate-100 bg-slate-50/40 shadow-inner">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead>
                                    <tr class="bg-gradient-to-r from-slate-100 to-slate-50">
                                        <th class="px-5 py-4 text-left text-xs font-bold uppercase text-slate-600">{{ __('shipments.order_number') }}</th>
                                        <th class="px-5 py-4 text-left text-xs font-bold uppercase text-slate-600">{{ __('shipments.recipient_name') }}</th>
                                        <th class="px-5 py-4 text-left text-xs font-bold uppercase text-slate-600">{{ __('shipments.destination_city') }}</th>
                                        <th class="px-5 py-4 text-left text-xs font-bold uppercase text-slate-600">{{ __('shipments.current_status') }}</th>
                                        <th class="px-5 py-4 text-right text-xs font-bold uppercase text-slate-600">{{ __('shipments.actions_column') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @foreach ($shipments as $shipment)
                                        <tr class="hover:bg-brand-50/50 transition-colors">
                                            <td class="px-5 py-4">
                                                <span class="inline-flex font-mono text-sm font-bold text-brand-900 bg-gradient-to-br from-brand-50 to-brand-100/80 px-3 py-1.5 rounded-xl ring-1 ring-brand-200/80 shadow-sm">{{ $shipment->tracking_number }}</span>
                                            </td>
                                            <td class="px-5 py-4 text-slate-900 font-medium">{{ $shipment->recipient_name }}</td>
                                            <td class="px-5 py-4 text-slate-600">{{ $shipment->destination_city }}</td>
                                            <td class="px-5 py-4"><x-shipment-status-badge :status="$shipment->status" /></td>
                                            <td class="px-5 py-4 text-right">
                                                <a href="{{ route('shipments.show', $shipment) }}" class="inline-flex items-center font-bold text-brand-600 hover:text-brand-800">{{ __('shipments.view_detail') }} →</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-8">{{ $shipments->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
