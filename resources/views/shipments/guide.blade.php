<x-app-layout>
    <x-slot name="header">
        <div class="print:hidden flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">{{ __('shipments.guide_title') }}</h2>
                <p class="font-mono text-sm text-slate-500 mt-1">{{ $shipment->tracking_number }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" onclick="window.print()" class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">{{ __('shipments.guide_print') }}</button>
                <a href="{{ route('shipments.guide.pdf', $shipment) }}" class="inline-flex items-center rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">{{ __('shipments.guide_pdf') }}</a>
                <a href="{{ route('shipments.show', $shipment) }}" class="inline-flex items-center rounded-lg border border-transparent px-4 py-2 text-sm font-medium text-brand-700 hover:text-brand-900">{{ __('shipments.back_to_list') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="py-10 print:py-4">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 px-4">
            <article class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden print:shadow-none print:border-slate-300">
                <div class="bg-gradient-to-r from-brand-700 to-brand-600 px-6 py-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <div class="bg-white/15 rounded-xl p-2 flex items-center justify-center">
                            @if (brand_logo_asset())
                                <img src="{{ brand_logo_asset() }}" alt="" class="h-12 w-auto max-w-[10rem] object-contain" />
                            @else
                                <svg viewBox="0 0 48 48" class="h-12 w-12" aria-hidden="true">
                                    <defs>
                                        <linearGradient id="gdg" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" stop-color="#fff" stop-opacity="0.95"/>
                                            <stop offset="100%" stop-color="#fff" stop-opacity="0.75"/>
                                        </linearGradient>
                                    </defs>
                                    <rect width="48" height="48" rx="12" fill="url(#gdg)"/>
                                    <text x="24" y="31" text-anchor="middle" font-family="system-ui" font-size="15" font-weight="700" fill="#1e40af">MP</text>
                                </svg>
                            @endif
                        </div>
                        <div>
                            <p class="text-white/90 text-sm font-medium">{{ __('brand.name') }}</p>
                            <p class="text-white text-lg font-semibold">{{ $shipment->organization->name }}</p>
                            <p class="text-brand-100 text-xs mt-1">{{ __('shipments.guide_brand_line') }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-brand-100 text-xs uppercase tracking-wide">{{ __('shipments.order_number') }}</p>
                        <p class="text-white font-mono text-xl tracking-wider">{{ $shipment->tracking_number }}</p>
                    </div>
                </div>

                <div class="p-6 md:p-8 space-y-8">
                    <div class="flex flex-col sm:flex-row gap-8 justify-between items-start">
                        <div class="space-y-1">
                            <p class="text-xs font-semibold text-slate-500 uppercase">{{ __('shipments.current_status') }}</p>
                            <p class="text-2xl font-bold text-slate-900">{{ $shipment->statusLabel() }}</p>
                            <p class="text-sm text-slate-500">{{ __('shipments.guide_printed_at') }}: {{ $printedAt->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="flex flex-col items-center sm:items-end gap-2">
                            <img src="{{ $qrDataUri }}" alt="" width="140" height="140" class="rounded-lg border border-slate-200 bg-white p-1" />
                            <p class="text-xs text-slate-500 text-center max-w-[10rem]">{{ __('shipments.guide_qr_label') }}</p>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-8">
                        <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-5">
                            <h3 class="text-sm font-semibold text-brand-800 uppercase tracking-wide mb-3">{{ __('shipments.sender_section') }}</h3>
                            <p class="font-medium text-slate-900">{{ $shipment->sender_name }}</p>
                            <p class="text-sm text-slate-600 mt-1">{{ $shipment->sender_phone }}</p>
                            @if ($shipment->sender_email)
                                <p class="text-sm text-slate-600">{{ $shipment->sender_email }}</p>
                            @endif
                            <div class="mt-4 pt-4 border-t border-slate-200">
                                <p class="text-xs font-semibold text-slate-500 uppercase">{{ __('shipments.origin_section') }}</p>
                                <p class="text-sm text-slate-800 mt-1 whitespace-pre-line">{{ $shipment->origin_address_line }}</p>
                                <p class="text-sm text-slate-600 mt-2">{{ $shipment->origin_city }}@if ($shipment->origin_region), {{ $shipment->origin_region }}@endif</p>
                            </div>
                        </div>
                        <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-5">
                            <h3 class="text-sm font-semibold text-brand-800 uppercase tracking-wide mb-3">{{ __('shipments.recipient_section') }}</h3>
                            <p class="font-medium text-slate-900">{{ $shipment->recipient_name }}</p>
                            <p class="text-sm text-slate-600 mt-1">{{ $shipment->recipient_phone }}</p>
                            @if ($shipment->recipient_email)
                                <p class="text-sm text-slate-600">{{ $shipment->recipient_email }}</p>
                            @endif
                            <div class="mt-4 pt-4 border-t border-slate-200">
                                <p class="text-xs font-semibold text-slate-500 uppercase">{{ __('shipments.destination_section') }}</p>
                                <p class="text-sm text-slate-800 mt-1 whitespace-pre-line">{{ $shipment->destination_address_line }}</p>
                                <p class="text-sm text-slate-600 mt-2">{{ $shipment->destination_city }}@if ($shipment->destination_region), {{ $shipment->destination_region }}@endif</p>
                            </div>
                        </div>
                    </div>

                    <p class="text-xs text-slate-400 border-t border-slate-100 pt-6">{{ __('tracking.public_disclaimer') }}</p>
                </div>
            </article>
        </div>
    </div>
</x-app-layout>
