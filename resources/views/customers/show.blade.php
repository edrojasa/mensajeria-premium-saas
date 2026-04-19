<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:justify-between sm:items-start">
            <div>
                <h2 class="font-display text-2xl font-bold text-slate-900">{{ $customer->name }}</h2>
                <p class="mt-1 text-sm text-slate-600">{{ __('customers.field_phone') }}: {{ $customer->phone }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('shipments.create') }}?customer_id={{ $customer->id }}" class="inline-flex items-center rounded-2xl bg-brand-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-brand-600/25 hover:bg-brand-700">{{ __('customers.create_shipment_for') }}</a>
                <a href="{{ route('customers.edit', $customer) }}" class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-800 hover:bg-slate-50">{{ __('customers.edit_action') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="py-10 md:py-12 space-y-8 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="rounded-3xl border border-slate-200 bg-white shadow-xl p-8 ring-1 ring-slate-900/5">
            <h3 class="font-display text-lg font-bold text-slate-900">{{ __('customers.section_general') }}</h3>
            <dl class="mt-4 grid gap-4 sm:grid-cols-2 text-sm">
                <div><dt class="text-slate-500">{{ __('customers.field_document') }}</dt><dd class="font-medium text-slate-900">{{ $customer->document ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">{{ __('customers.field_email') }}</dt><dd class="font-medium text-slate-900">{{ $customer->email ?? '—' }}</dd></div>
                @if ($customer->notes)
                    <div class="sm:col-span-2"><dt class="text-slate-500">{{ __('customers.field_notes') }}</dt><dd class="text-slate-800">{{ $customer->notes }}</dd></div>
                @endif
            </dl>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white shadow-xl p-8 ring-1 ring-slate-900/5">
            <h3 class="font-display text-lg font-bold text-slate-900">{{ __('customers.section_addresses') }}</h3>
            @forelse ($customer->addresses as $addr)
                <div class="mt-4 rounded-2xl border border-slate-100 bg-slate-50/80 p-4 flex justify-between gap-4">
                    <div>
                        <p class="font-semibold text-slate-900">{{ $addr->label }} @if ($addr->is_default)<span class="text-xs font-bold text-brand-700">({{ __('customers.address_default') }})</span>@endif</p>
                        <p class="text-sm text-slate-700 mt-1">{{ $addr->address_line }}</p>
                        <p class="text-xs text-slate-500 mt-1">{{ $addr->city }} @if ($addr->department) · {{ $addr->department }} @endif</p>
                    </div>
                </div>
            @empty
                <p class="mt-4 text-sm text-slate-600">{{ __('customers.empty_addresses') }}</p>
            @endforelse
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white shadow-xl overflow-hidden ring-1 ring-slate-900/5">
            <div class="border-b border-slate-100 px-8 py-4 bg-gradient-to-r from-slate-50 to-brand-50/40">
                <h3 class="font-display text-lg font-bold text-slate-900">{{ __('customers.section_shipments') }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-600">{{ __('shipments.order_number') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-600">{{ __('shipments.current_status') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-bold uppercase text-slate-600">{{ __('shipments.actions_column') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($shipments as $shipment)
                            <tr>
                                <td class="px-6 py-3 font-mono font-bold text-brand-900">{{ $shipment->tracking_number }}</td>
                                <td class="px-6 py-3"><x-shipment-status-badge :status="$shipment->status" /></td>
                                <td class="px-6 py-3 text-right"><a href="{{ route('shipments.show', $shipment) }}" class="font-bold text-brand-600">{{ __('customers.view_detail') }}</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-8 text-center text-slate-600">{{ __('shipments.empty') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4">{{ $shipments->links() }}</div>
        </div>
    </div>
</x-app-layout>
