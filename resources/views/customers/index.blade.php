<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:justify-between sm:items-start">
            <div>
                <h2 class="font-display text-2xl md:text-3xl font-bold text-slate-900">{{ __('customers.title') }}</h2>
                <p class="mt-2 text-sm text-slate-600">{{ __('customers.subtitle_index') }}</p>
            </div>
            <a href="{{ route('customers.create') }}" class="inline-flex justify-center items-center rounded-2xl bg-brand-600 px-6 py-3 text-base font-bold text-white shadow-xl shadow-brand-600/30 hover:bg-brand-700 shrink-0">{{ __('customers.create_action') }}</a>
        </div>
    </x-slot>

    <div class="py-10 md:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-4 space-y-6">
            @if (auth()->user()->canExportTenantReports())
                <div class="flex flex-col sm:flex-row flex-wrap gap-2 sm:gap-3 justify-end items-stretch sm:items-center">
                    <x-export-link variant="primary" href="{{ route('exports.customers.excel', request()->query()) }}" class="w-full sm:w-auto justify-center">{{ __('exports.excel') }}</x-export-link>
                    <x-export-link href="{{ route('exports.customers.pdf', request()->query()) }}" class="w-full sm:w-auto justify-center">{{ __('exports.pdf') }}</x-export-link>
                </div>
            @endif

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl ring-1 ring-slate-900/5">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">{{ __('customers.filters_heading') }}</p>
                <form method="GET" action="{{ route('customers.index') }}" class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-end">
                    <div class="flex-1">
                        <x-input-label for="customer_q" :value="__('customers.search_placeholder')" />
                        <x-text-input id="customer_q" name="q" type="search" class="mt-2 block w-full rounded-2xl" :value="request('q')" />
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                        <x-primary-button type="submit" class="rounded-2xl min-h-[2.75rem] justify-center px-5">{{ __('customers.search_submit') }}</x-primary-button>
                        <a href="{{ route('customers.index') }}" class="inline-flex min-h-[2.75rem] items-center justify-center whitespace-nowrap rounded-2xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">{{ __('shipments.filter_clear') }}</a>
                    </div>
                </form>
            </div>

            <div class="rounded-3xl border border-slate-200/90 bg-white shadow-2xl shadow-slate-900/10 overflow-hidden ring-1 ring-slate-900/5">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead>
                            <tr class="bg-gradient-to-r from-slate-100 to-slate-50">
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-600">{{ __('customers.field_name') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-600">{{ __('customers.field_customer_code') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-600">{{ __('customers.field_phone') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-600">{{ __('customers.field_email') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-600">{{ __('customers.shipments_count') }}</th>
                                <th class="px-6 py-4 text-right text-xs font-bold uppercase text-slate-600">{{ __('shipments.actions_column') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($customers as $customer)
                                <tr class="hover:bg-brand-50/40">
                                    <td class="px-6 py-4 font-semibold text-slate-900">{{ $customer->name }}</td>
                                    <td class="px-6 py-4 font-mono text-sm text-slate-800">{{ $customer->customer_code }}</td>
                                    <td class="px-6 py-4 text-slate-700">{{ $customer->phone }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $customer->email ?? '—' }}</td>
                                    <td class="px-6 py-4"><span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-800">{{ $customer->shipments_count }}</span></td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('customers.show', $customer) }}" class="font-bold text-brand-600 hover:text-brand-800">{{ __('customers.view_detail') }} →</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-14 text-center text-slate-600">{{ __('customers.empty_index') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($customers->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100">{{ $customers->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
