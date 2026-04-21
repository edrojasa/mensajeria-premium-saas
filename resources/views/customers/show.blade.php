<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:justify-between sm:items-start">
            <div>
                <h2 class="font-display text-2xl font-bold text-slate-900">{{ $customer->name }}</h2>
                <p class="mt-1 text-sm font-mono text-brand-800">{{ $customer->customer_code }}</p>
                <p class="mt-1 text-sm text-slate-600">{{ __('customers.field_phone') }}: {{ $customer->phone }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if ($customer->is_active)
                    <a href="{{ route('shipments.create') }}?customer_id={{ $customer->id }}" class="inline-flex items-center rounded-2xl bg-brand-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-brand-600/25 hover:bg-brand-700">{{ __('customers.create_shipment_for') }}</a>
                @endif
                @can('update', $customer)
                    <a href="{{ route('customers.edit', $customer) }}" class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-800 hover:bg-slate-50">{{ __('customers.edit_action') }}</a>
                @endcan
                @can('deactivate', $customer)
                    @if ($customer->is_active)
                        <form action="{{ route('customers.deactivate', $customer) }}" method="POST" class="inline" onsubmit="return confirm(@json(__('customers.deactivate_confirm')));">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="inline-flex items-center rounded-2xl border border-amber-200 bg-amber-50 px-5 py-3 text-sm font-semibold text-amber-900 hover:bg-amber-100">{{ __('customers.action_deactivate') }}</button>
                        </form>
                    @else
                        <form action="{{ route('customers.activate', $customer) }}" method="POST" class="inline" onsubmit="return confirm(@json(__('customers.activate_confirm')));">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="inline-flex items-center rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-semibold text-emerald-900 hover:bg-emerald-100">{{ __('customers.action_activate') }}</button>
                        </form>
                    @endif
                @endcan
                @can('forceDestroy', $customer)
                    <form action="{{ route('customers.force-destroy', $customer) }}" method="POST" class="inline" onsubmit="return confirm(@json(__('customers.force_delete_confirm')));">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center rounded-2xl border border-red-200 bg-red-50 px-5 py-3 text-sm font-semibold text-red-900 hover:bg-red-100">{{ __('customers.action_force_delete') }}</button>
                    </form>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-10 md:py-12 space-y-8 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="rounded-3xl border border-slate-200 bg-white shadow-xl p-8 ring-1 ring-slate-900/5">
            <h3 class="font-display text-lg font-bold text-slate-900">{{ __('customers.section_general') }}</h3>
            <dl class="mt-4 grid gap-4 sm:grid-cols-2 text-sm">
                <div><dt class="text-slate-500">{{ __('customers.field_customer_code') }}</dt><dd class="font-mono font-semibold text-brand-900">{{ $customer->customer_code }}</dd></div>
                <div><dt class="text-slate-500">{{ __('customers.field_document') }}</dt><dd class="font-medium text-slate-900">{{ $customer->document ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">{{ __('customers.field_email') }}</dt><dd class="font-medium text-slate-900">{{ $customer->email ?? '—' }}</dd></div>
                <div>
                    <dt class="text-slate-500">{{ __('customers.field_status_column') }}</dt>
                    <dd class="font-medium">
                        @if ($customer->is_active)
                            <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-900">{{ __('customers.status_active_upper') }}</span>
                        @else
                            <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-900">{{ __('customers.status_inactive_upper') }}</span>
                        @endif
                    </dd>
                </div>
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
            <div class="border-b border-slate-100 px-8 py-4 bg-gradient-to-r from-slate-50 to-brand-50/40 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                @if ($canFinance)
                    <nav class="flex flex-wrap gap-2" aria-label="{{ __('customers.tabs_aria') }}">
                        <a href="{{ route('customers.show', ['customer' => $customer, 'tab' => 'shipments']) }}" class="inline-flex items-center rounded-xl px-4 py-2 text-sm font-bold transition {{ $activeTab === 'shipments' ? 'bg-brand-600 text-white shadow-md' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}">{{ __('customers.section_shipments') }}</a>
                        <a href="{{ route('customers.show', ['customer' => $customer, 'tab' => 'financial']) }}" class="inline-flex items-center rounded-xl px-4 py-2 text-sm font-bold transition {{ $activeTab === 'financial' ? 'bg-emerald-700 text-white shadow-md' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}">{{ __('customers.tab_financial') }}</a>
                    </nav>
                @else
                    <h3 class="font-display text-lg font-bold text-slate-900">{{ __('customers.section_shipments') }}</h3>
                @endif
            </div>

            @if ($activeTab === 'shipments')
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
            @else
                <div class="px-8 pt-6 pb-2">
                    <div class="rounded-3xl border border-emerald-100 bg-emerald-50/50 shadow-inner p-6 ring-1 ring-emerald-900/5">
                        <h3 class="font-display text-lg font-bold text-emerald-950">{{ __('finance.customer_finance_title') }}</h3>
                        <dl class="mt-4 grid gap-4 sm:grid-cols-3 text-sm">
                            <div>
                                <dt class="text-slate-600">{{ __('finance.customer_finance_billed') }}</dt>
                                <dd class="mt-1 text-xl font-bold text-slate-900">${{ number_format((float) $financialBilled, 2, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-600">{{ __('finance.customer_finance_paid') }}</dt>
                                <dd class="mt-1 text-xl font-bold text-emerald-800">${{ number_format((float) $financialPaid, 2, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-600">{{ __('finance.customer_finance_balance') }}</dt>
                                <dd class="mt-1 text-xl font-bold text-amber-900">${{ number_format((float) $financialBalance, 2, ',', '.') }}</dd>
                            </div>
                        </dl>
                    </div>
                    <div class="mt-4 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                        <p class="text-sm font-semibold text-slate-800">{{ __('finance.customer_financial_shipments_detail') }}</p>
                        <div class="flex gap-2">
                            <a href="{{ route('customers.financial.pdf', ['customer' => $customer] + request()->query()) }}" class="inline-flex rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">{{ __('exports.pdf') }}</a>
                            <a href="{{ route('customers.financial.excel', ['customer' => $customer] + request()->query()) }}" class="inline-flex rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">{{ __('exports.excel') }}</a>
                        </div>
                    </div>
                    <form method="GET" action="{{ route('customers.show', $customer) }}" class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-3">
                        <input type="hidden" name="tab" value="financial">
                        <input type="date" name="from" value="{{ request('from') }}" class="rounded-xl border-slate-300" />
                        <input type="date" name="to" value="{{ request('to') }}" class="rounded-xl border-slate-300" />
                        <select name="status" class="rounded-xl border-slate-300">
                            <option value="">{{ __('finance.filter_status') }}</option>
                            <option value="pending" @selected(request('status') === 'pending')>{{ __('finance.payment_statuses.pending') }}</option>
                            <option value="paid" @selected(request('status') === 'paid')>{{ __('finance.payment_statuses.paid') }}</option>
                        </select>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('finance.filter_search') }}" class="rounded-xl border-slate-300" />
                        <div class="md:col-span-4 flex gap-2">
                            <button type="submit" class="inline-flex rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white">{{ __('shipments.filter_apply') }}</button>
                            <a href="{{ route('customers.show', ['customer' => $customer, 'tab' => 'financial']) }}" class="inline-flex rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">{{ __('shipments.filter_clear') }}</a>
                        </div>
                    </form>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50">
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-600">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-600">{{ __('shipments.order_number') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-bold uppercase text-slate-600">Valor</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-600">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-600">Usuario</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-600">Tipo</th>
                                <th class="px-6 py-3 text-right text-xs font-bold uppercase text-slate-600">{{ __('shipments.actions_column') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($financialMovements ?? [] as $movement)
                                <tr>
                                    <td class="px-6 py-3">{{ optional($movement['date'])->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-3 font-mono font-bold text-brand-900">{{ $movement['tracking'] }}</td>
                                    <td class="px-6 py-3 text-right text-slate-800">${{ number_format((float) $movement['value'], 2, ',', '.') }}</td>
                                    <td class="px-6 py-3 text-slate-700">{{ \App\Finance\PaymentStatus::label($movement['status']) }}</td>
                                    <td class="px-6 py-3 text-slate-700">{{ $movement['user'] }}</td>
                                    <td class="px-6 py-3 text-slate-700">{{ $movement['type'] === 'pago' ? __('finance.type_payment') : __('finance.type_charge') }}</td>
                                    <td class="px-6 py-3 text-right"><a href="{{ route('shipments.show', $movement['shipment']) }}" class="font-bold text-brand-600">{{ __('customers.view_detail') }}</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-6 py-8 text-center text-slate-600">{{ __('shipments.empty') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4">{{ $financialMovements?->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
