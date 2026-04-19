<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-display text-2xl md:text-3xl font-bold text-slate-900 tracking-tight">{{ __('shipments.title') }}</h2>
            <p class="mt-2 text-sm md:text-base text-slate-600">{{ __('shipments.subtitle_index') }}</p>
        </div>
    </x-slot>

    <div class="py-10 md:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-4">
            <x-auth-session-status class="mb-6" :status="session('status')" />

            <div class="rounded-3xl border border-slate-200/90 bg-white shadow-2xl shadow-slate-900/10 overflow-hidden ring-1 ring-slate-900/5">
                {{-- Toolbar del módulo: título contextual + crear envío (visible siempre) --}}
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between px-6 sm:px-8 py-6 border-b border-slate-100 bg-gradient-to-r from-slate-50 via-white to-brand-50/50">
                    <div class="flex items-start gap-4 min-w-0">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-brand-600 text-white shadow-lg shadow-brand-600/30">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </span>
                        <div class="min-w-0">
                            <h3 class="font-display text-lg font-bold text-slate-900">{{ __('shipments.module_heading') }}</h3>
                            <p class="text-sm text-slate-600">{{ __('shipments.index_table_hint') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('shipments.create') }}" class="inline-flex w-full sm:w-auto justify-center items-center gap-2 rounded-2xl bg-brand-600 px-6 py-3.5 text-base font-bold text-white shadow-xl shadow-brand-600/30 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition shrink-0">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        {{ __('shipments.create_action') }}
                    </a>
                </div>

                <div class="p-5 sm:p-8">
                    @if ($shipments->isEmpty())
                        <div class="text-center py-16 px-4 rounded-2xl bg-gradient-to-b from-slate-50 to-white border border-dashed border-slate-200">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-white text-slate-400 shadow-inner mb-5 ring-1 ring-slate-100">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V7a2 2 0 00-2-2H6L4 5v14a2 2 0 002 2h12a2 2 0 002-2v-3"/></svg>
                            </div>
                            <p class="text-slate-700 font-semibold text-lg">{{ __('shipments.empty') }}</p>
                            <a href="{{ route('shipments.create') }}" class="mt-6 inline-flex items-center justify-center rounded-2xl bg-brand-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-brand-600/25 hover:bg-brand-700">{{ __('shipments.create_action') }}</a>
                        </div>
                    @else
                        <div class="overflow-x-auto rounded-2xl border border-slate-100 bg-slate-50/40 shadow-inner">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead>
                                    <tr class="bg-gradient-to-r from-slate-100 to-slate-50">
                                        <th scope="col" class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-600">{{ __('shipments.order_number') }}</th>
                                        <th scope="col" class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-600">{{ __('shipments.recipient_name') }}</th>
                                        <th scope="col" class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-600">{{ __('shipments.destination_city') }}</th>
                                        <th scope="col" class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-600">{{ __('shipments.current_status') }}</th>
                                        <th scope="col" class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-600">{{ __('shipments.created_at_column') }}</th>
                                        <th scope="col" class="px-5 py-4 text-right text-xs font-bold uppercase tracking-wide text-slate-600">{{ __('shipments.actions_column') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @foreach ($shipments as $shipment)
                                        <tr class="hover:bg-brand-50/50 transition-colors">
                                            <td class="px-5 py-4 align-middle">
                                                <span class="inline-flex font-mono text-sm font-bold text-brand-900 bg-gradient-to-br from-brand-50 to-brand-100/80 px-3 py-1.5 rounded-xl ring-1 ring-brand-200/80 shadow-sm">{{ $shipment->tracking_number }}</span>
                                            </td>
                                            <td class="px-5 py-4 text-slate-900 font-medium">{{ $shipment->recipient_name }}</td>
                                            <td class="px-5 py-4 text-slate-600">{{ $shipment->destination_city }}</td>
                                            <td class="px-5 py-4">
                                                <x-shipment-status-badge :status="$shipment->status" />
                                            </td>
                                            <td class="px-5 py-4 whitespace-nowrap text-slate-600">{{ $shipment->created_at->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</td>
                                            <td class="px-5 py-4 text-right">
                                                <a href="{{ route('shipments.show', $shipment) }}" class="inline-flex items-center font-bold text-brand-600 hover:text-brand-800">{{ __('shipments.view_detail') }} →</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-8">
                            {{ $shipments->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
