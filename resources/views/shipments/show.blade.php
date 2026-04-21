@php
    use App\Shipments\ShipmentStatus;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('shipments.order_number') }}</p>
                <h2 class="font-mono text-xl font-bold text-brand-800 mt-1 tracking-wide">{{ $shipment->tracking_number }}</h2>
                <p class="text-sm text-slate-600 mt-1">{{ __('shipments.subtitle_show') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @can('update', $shipment)
                    <a href="{{ route('shipments.edit', $shipment) }}" class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-bold text-white shadow-lg hover:bg-slate-800 transition">
                        {{ __('shipments.edit_shipment') }}
                    </a>
                @endcan
                <a href="{{ route('shipments.guide', $shipment) }}" class="inline-flex items-center rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition">
                    {{ __('shipments.guide_view') }}
                </a>
                <a href="{{ route('shipments.guide.pdf', $shipment) }}" class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    {{ __('shipments.guide_pdf') }}
                </a>
                @can('viewReport', $shipment)
                    <a href="{{ route('shipments.report.pdf', $shipment) }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 shadow-sm">
                        {{ __('shipments.download_report_pdf') }}
                    </a>
                @endcan
                @can('deactivate', $shipment)
                    @if ($shipment->status !== \App\Shipments\ShipmentStatus::DELIVERED && $shipment->status !== \App\Shipments\ShipmentStatus::CANCELLED)
                        <form method="POST" action="{{ route('shipments.deactivate', $shipment) }}" class="inline" onsubmit="return confirm(@json(__('shipments.confirm_deactivate')));">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-800 hover:bg-red-100">
                                {{ __('shipments.deactivate_action') }}
                            </button>
                        </form>
                    @endif
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 px-4 space-y-8">
            <x-auth-session-status class="mb-2" :status="session('status')" />

            @if ($shipment->status === ShipmentStatus::INCIDENT)
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ __('shipments.timeline_incident_banner') }}
                </div>
            @endif

            @if (!empty($timelineCancelled))
                <div class="rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-800">
                    {{ __('shipments.cancelled_banner') }}
                </div>
            @endif

            {{-- Resumen --}}
            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-slate-100 bg-gradient-to-r from-slate-50 to-brand-50/40 px-6 py-4">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-600">{{ __('shipments.show_summary_title') }}</h3>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <p class="text-xs font-medium text-slate-500">{{ __('shipments.order_number') }}</p>
                        <p class="mt-1 font-mono text-lg font-bold text-brand-800">{{ $shipment->tracking_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500">{{ __('shipments.current_status') }}</p>
                        <div class="mt-2">
                            <x-shipment-status-badge :status="$shipment->status" class="text-sm px-3 py-1" />
                        </div>
                    </div>
                    @if ($shipment->relationLoaded('customer') && $shipment->customer)
                        <div>
                            <p class="text-xs font-medium text-slate-500">{{ __('shipments.customer_linked') }}</p>
                            @can('view', $shipment->customer)
                                <p class="mt-1 font-semibold text-slate-900">
                                    <a href="{{ route('customers.show', $shipment->customer) }}" class="text-brand-700 hover:text-brand-900">{{ $shipment->customer->name }}</a>
                                </p>
                            @else
                                <p class="mt-1 font-semibold text-slate-900">{{ $shipment->customer->name }}</p>
                            @endcan
                        </div>
                    @endif
                    @if ($shipment->relationLoaded('assignedCourier') && $shipment->assignedCourier)
                        <div>
                            <p class="text-xs font-medium text-slate-500">{{ __('shipments.courier_assigned') }}</p>
                            <p class="mt-1 text-slate-900">{{ $shipment->assignedCourier->name }}</p>
                        </div>
                    @endif
                    @if ($shipment->reference_internal)
                        <div>
                            <p class="text-xs font-medium text-slate-500">{{ __('shipments.reference_internal') }}</p>
                            <p class="mt-1 text-slate-900">{{ $shipment->reference_internal }}</p>
                        </div>
                    @endif
                    @if ($shipment->weight_kg !== null)
                        <div>
                            <p class="text-xs font-medium text-slate-500">{{ __('shipments.weight_kg') }}</p>
                            <p class="mt-1 text-slate-900">{{ $shipment->weight_kg }}</p>
                        </div>
                    @endif
                    @if ($shipment->declared_value !== null)
                        <div>
                            <p class="text-xs font-medium text-slate-500">{{ __('shipments.declared_value') }}</p>
                            <p class="mt-1 text-slate-900">{{ number_format((float) $shipment->declared_value, 2, ',', '.') }}</p>
                        </div>
                    @endif
                </div>
            </section>

            @if (auth()->user()->canAccessFinancialModule())
                <section class="rounded-2xl border border-emerald-100 bg-emerald-50/40 shadow-sm overflow-hidden">
                    <div class="border-b border-emerald-100 bg-white/80 px-6 py-4">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-emerald-900">{{ __('finance.section_shipment_finance') }}</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <p class="text-xs font-medium text-slate-500">{{ __('finance.field_service_type') }}</p>
                            <p class="mt-1 font-semibold text-slate-900">{{ $shipment->service_type ? \App\Finance\ServiceType::label($shipment->service_type) : '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-slate-500">{{ __('finance.field_distance_km') }}</p>
                            <p class="mt-1 text-slate-900">{{ $shipment->distance_km ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-slate-500">{{ __('finance.cost_calculated') }}</p>
                            <p class="mt-1 text-xl font-bold text-emerald-800">{{ $shipment->cost !== null ? '$'.number_format((float) $shipment->cost, 2, ',', '.') : '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-slate-500">{{ __('finance.field_payment_type') }}</p>
                            <p class="mt-1 font-semibold text-slate-900">{{ $shipment->payment_type ? \App\Finance\PaymentType::label($shipment->payment_type) : '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-slate-500">{{ __('finance.field_payment_status') }}</p>
                            <p class="mt-1"><span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $shipment->payment_status === \App\Finance\PaymentStatus::PAID ? 'bg-emerald-100 text-emerald-900' : 'bg-amber-100 text-amber-900' }}">{{ $shipment->payment_status ? \App\Finance\PaymentStatus::label($shipment->payment_status) : '—' }}</span></p>
                        </div>
                        @if ($shipment->paid_amount !== null)
                            <div>
                                <p class="text-xs font-medium text-slate-500">{{ __('finance.field_paid_amount') }}</p>
                                <p class="mt-1 font-semibold text-slate-900">${{ number_format((float) $shipment->paid_amount, 2, ',', '.') }}</p>
                            </div>
                        @endif
                        @if ($shipment->payment_date)
                            <div>
                                <p class="text-xs font-medium text-slate-500">{{ __('finance.field_payment_date') }}</p>
                                <p class="mt-1 text-slate-900">{{ $shipment->payment_date->timezone(config('app.timezone'))->format('d/m/Y') }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="border-t border-emerald-100 px-6 py-5 bg-white">
                        <h4 class="text-xs font-bold uppercase tracking-wide text-slate-600">{{ __('finance.payment_section') }}</h4>
                        <form method="POST" action="{{ route('shipments.payment.update', $shipment) }}" class="mt-4 grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                            @csrf
                            @method('PATCH')
                            <div class="md:col-span-3">
                                <x-input-label for="payment_status" :value="__('finance.field_payment_status')" />
                                <select id="payment_status" name="payment_status" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm">
                                    @foreach (\App\Finance\PaymentStatus::all() as $ps)
                                        <option value="{{ $ps }}" @selected(old('payment_status', $shipment->payment_status) === $ps)>{{ \App\Finance\PaymentStatus::label($ps) }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('payment_status')" class="mt-2" />
                            </div>
                            <div class="md:col-span-3">
                                <x-input-label for="paid_amount" :value="__('finance.field_paid_amount')" />
                                <x-text-input id="paid_amount" name="paid_amount" type="number" step="0.01" min="0" class="mt-1 block w-full rounded-xl" :value="old('paid_amount', $shipment->paid_amount)" />
                                <x-input-error :messages="$errors->get('paid_amount')" class="mt-2" />
                            </div>
                            <div class="md:col-span-3">
                                <x-input-label for="payment_date" :value="__('finance.field_payment_date')" />
                                <x-text-input id="payment_date" name="payment_date" type="date" class="mt-1 block w-full rounded-xl" :value="old('payment_date', $shipment->payment_date?->format('Y-m-d'))" />
                                <x-input-error :messages="$errors->get('payment_date')" class="mt-2" />
                            </div>
                            <div class="md:col-span-3">
                                <x-primary-button type="submit" class="w-full md:w-auto rounded-2xl">{{ __('finance.record_payment') }}</x-primary-button>
                            </div>
                        </form>
                    </div>
                </section>
            @endif

            <section class="rounded-2xl border border-slate-200 bg-white shadow-lg shadow-slate-900/5 p-6 md:p-8">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-600">{{ __('shipments.evidence_section_title') }}</h3>
                @can('addEvidence', $shipment)
                    <form action="{{ route('shipments.evidences.store', $shipment) }}" method="POST" enctype="multipart/form-data" class="mt-4 grid gap-4 md:grid-cols-12 md:items-end border border-slate-100 rounded-xl p-4 bg-slate-50/80">
                        @csrf
                        <div class="md:col-span-8">
                            <x-input-label for="ev_note" :value="__('shipments.evidence_note_label')" />
                            <textarea id="ev_note" name="note" rows="2" class="mt-1 block w-full rounded-xl border-slate-300">{{ old('note') }}</textarea>
                            <x-input-error :messages="$errors->get('note')" class="mt-2" />
                        </div>
                        <div class="md:col-span-4">
                            <x-input-label for="ev_image" :value="__('shipments.evidence_image_label')" />
                            <input id="ev_image" name="image" type="file" accept=".jpg,.jpeg,.png,image/jpeg,image/png" class="mt-1 block w-full text-sm text-slate-600" />
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>
                        <div class="md:col-span-12 flex justify-end">
                            <x-primary-button type="submit">{{ __('shipments.evidence_submit') }}</x-primary-button>
                        </div>
                    </form>
                @endcan
                @if ($shipment->relationLoaded('evidences') && $shipment->evidences->isNotEmpty())
                    <ul class="mt-6 space-y-4 divide-y divide-slate-100">
                        @foreach ($shipment->evidences as $evidence)
                            <li class="pt-4 first:pt-0 flex flex-col gap-2 md:flex-row md:gap-6">
                                <div class="flex-1 text-sm">
                                    <p class="font-semibold text-slate-900">{{ $evidence->author?->name ?? '—' }}</p>
                                    <p class="text-xs text-slate-500">{{ $evidence->created_at->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</p>
                                    @if ($evidence->note)
                                        <p class="mt-2 text-slate-700 whitespace-pre-line">{{ $evidence->note }}</p>
                                    @endif
                                </div>
                                @if ($evidence->image_path)
                                    <div class="shrink-0">
                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($evidence->image_path) }}" alt="" class="max-h-48 rounded-xl border border-slate-200 shadow-sm">
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="mt-4 text-sm text-slate-600">{{ __('shipments.evidence_none_yet') }}</p>
                @endif
            </section>

            {{-- Timeline --}}
            @unless (!empty($timelineCancelled))
            <section class="rounded-2xl border border-slate-200 bg-white shadow-lg shadow-slate-900/5 p-6 md:p-8">
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-600">{{ __('shipments.timeline_title') }}</h3>
                        <p class="text-xs text-slate-500 mt-1">{{ __('shipments.timeline_progress_hint') }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-bold text-brand-700">{{ $timelineProgressPercent }}%</span>
                        <span class="block text-xs text-slate-500">{{ __('shipments.timeline_progress_label') }}</span>
                    </div>
                </div>
                <div class="mb-8">
                    <div class="h-3 rounded-full bg-slate-200 overflow-hidden shadow-inner ring-1 ring-slate-200/80">
                        <div
                            class="h-full rounded-full bg-gradient-to-r from-brand-500 via-brand-600 to-indigo-600 transition-all duration-700 ease-out shadow-sm"
                            style="width: {{ $timelineProgressPercent }}%"
                            role="progressbar"
                            aria-valuenow="{{ $timelineProgressPercent }}"
                            aria-valuemin="0"
                            aria-valuemax="100"
                        ></div>
                    </div>
                </div>
                <div class="relative">
                    <div class="hidden md:block absolute top-8 left-0 right-0 h-1 bg-slate-200 rounded-full" aria-hidden="true"></div>
                    <ol class="grid grid-cols-1 md:grid-cols-4 gap-8 md:gap-4 relative">
                        @foreach ($timelineSteps as $step)
                            @php
                                $isDone = $step['phase'] === 'done';
                                $isCurrent = $step['phase'] === 'current';
                                $isIncident = $step['phase'] === 'incident';
                                $ring = $isCurrent ? 'ring-2 ring-brand-500 ring-offset-2' : ($isIncident ? 'ring-2 ring-red-500 ring-offset-2' : '');
                                if ($isIncident) {
                                    $bg = 'bg-red-600 text-white';
                                } elseif ($isCurrent) {
                                    $bg = 'bg-brand-600 text-white';
                                } elseif ($isDone) {
                                    $bg = 'bg-emerald-500 text-white';
                                } else {
                                    $bg = 'bg-slate-200 text-slate-500';
                                }
                            @endphp
                            <li class="flex md:flex-col md:items-center md:text-center gap-4 md:gap-3">
                                <div class="flex md:flex-col items-center gap-3 md:gap-3">
                                    <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl {{ $bg }} {{ $ring }} shadow-sm">
                                        @if ($step['key'] === ShipmentStatus::RECEIVED)
                                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V7a2 2 0 00-2-2H6L4 5v14a2 2 0 002 2h12a2 2 0 002-2v-3"/></svg>
                                        @elseif ($step['key'] === ShipmentStatus::IN_TRANSIT)
                                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h8m-8 4h5M5 3h14a2 2 0 012 2v11h-4m-8 0H5m0 0l-2-6m2 6h12"/></svg>
                                        @elseif ($step['key'] === ShipmentStatus::OUT_FOR_DELIVERY)
                                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"/></svg>
                                        @else
                                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @endif
                                    </span>
                                    <div class="md:px-1">
                                        <p class="font-semibold text-slate-900">{{ $step['label'] }}</p>
                                        <p class="text-xs mt-1 font-medium {{ $isCurrent ? 'text-brand-600' : ($isIncident ? 'text-red-600' : ($isDone ? 'text-emerald-600' : 'text-slate-400')) }}">
                                            @if ($isDone)
                                                {{ __('shipments.timeline_done') }}
                                            @elseif ($isCurrent)
                                                {{ __('shipments.timeline_active') }}
                                            @elseif ($isIncident)
                                                {{ __('shipments.timeline_incident') }}
                                            @else
                                                {{ __('shipments.timeline_pending') }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </section>
            @endunless

            {{-- Personas y rutas --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <section class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-brand-800 uppercase tracking-wide mb-4">{{ __('shipments.sender_section') }}</h3>
                    <ul class="text-sm text-slate-700 space-y-1">
                        <li class="font-medium text-slate-900">{{ $shipment->sender_name }}</li>
                        <li>{{ $shipment->sender_phone }}</li>
                        @if ($shipment->sender_email)
                            <li>{{ $shipment->sender_email }}</li>
                        @endif
                    </ul>
                    <div class="mt-6 pt-6 border-t border-slate-100">
                        <h4 class="text-xs font-semibold text-slate-500 uppercase mb-2">{{ __('shipments.origin_section') }}</h4>
                        <p class="text-sm text-slate-700 whitespace-pre-line">{{ $shipment->origin_address_line }}</p>
                        <p class="text-sm text-slate-500 mt-2">{{ $shipment->origin_city }}@if ($shipment->origin_region), {{ $shipment->origin_region }}@endif @if ($shipment->origin_postal_code) — {{ $shipment->origin_postal_code }} @endif</p>
                    </div>
                </section>
                <section class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-brand-800 uppercase tracking-wide mb-4">{{ __('shipments.recipient_section') }}</h3>
                    <ul class="text-sm text-slate-700 space-y-1">
                        <li class="font-medium text-slate-900">{{ $shipment->recipient_name }}</li>
                        <li>{{ $shipment->recipient_phone }}</li>
                        @if ($shipment->recipient_email)
                            <li>{{ $shipment->recipient_email }}</li>
                        @endif
                    </ul>
                    <div class="mt-6 pt-6 border-t border-slate-100">
                        <h4 class="text-xs font-semibold text-slate-500 uppercase mb-2">{{ __('shipments.destination_section') }}</h4>
                        <p class="text-sm text-slate-700 whitespace-pre-line">{{ $shipment->destination_address_line }}</p>
                        <p class="text-sm text-slate-500 mt-2">{{ $shipment->destination_city }}@if ($shipment->destination_region), {{ $shipment->destination_region }}@endif @if ($shipment->destination_postal_code) — {{ $shipment->destination_postal_code }} @endif</p>
                    </div>
                </section>
            </div>

            @if ($shipment->notes_internal)
                <section class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6">
                    <h3 class="font-semibold text-slate-900 mb-2">{{ __('shipments.notes_internal') }}</h3>
                    <p class="text-sm text-slate-700 whitespace-pre-line">{{ $shipment->notes_internal }}</p>
                </section>
            @endif

            @isset($currentOrganization)
                <section class="rounded-2xl border border-brand-100 bg-brand-50/50 p-6">
                    <h3 class="font-semibold text-slate-900 mb-1">{{ __('shipments.public_tracking_link') }}</h3>
                    <p class="text-xs text-slate-600 mb-3">{{ __('shipments.public_tracking_copy_hint') }}</p>
                    <code class="block text-sm bg-white p-3 rounded-xl border border-brand-100 text-brand-900 break-all">{{ route('tracking.public', ['organization_slug' => $currentOrganization->slug, 'tracking_number' => $shipment->tracking_number]) }}</code>
                </section>
            @endisset

            @can('updateStatus', $shipment)
            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6">
                <h3 class="font-semibold text-slate-900 mb-4">{{ __('shipments.change_status_section') }}</h3>
                @if (count($statusOptions) > 0)
                    <form method="POST" action="{{ route('shipments.status.update', $shipment) }}" class="space-y-4 max-w-xl">
                        @csrf
                        <div>
                            <x-input-label for="status" :value="__('shipments.new_status')" />
                            <select id="status" name="status" class="mt-1 block w-full border-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-xl shadow-sm" required>
                                @foreach ($statusOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('status')" />
                        </div>
                        <div>
                            <x-input-label for="notes" :value="__('shipments.status_notes')" />
                            <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border-slate-300 focus:border-brand-500 focus:ring-brand-500 rounded-xl shadow-sm" placeholder="{{ __('shipments.status_notes_help') }}">{{ old('notes') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                        </div>
                        <x-primary-button>{{ __('shipments.update_status_button') }}</x-primary-button>
                    </form>
                @else
                    <p class="text-sm text-slate-600">{{ __('shipments.no_status_changes') }}</p>
                @endif
            </section>
            @endcan

            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6">
                <h3 class="font-semibold text-slate-900 mb-4">{{ __('shipments.history_section') }}</h3>
                @if ($shipment->statusHistories->isEmpty())
                    <p class="text-sm text-slate-600">{{ __('shipments.history_empty') }}</p>
                @else
                    <div class="overflow-x-auto rounded-xl border border-slate-100">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-700">{{ __('shipments.history_at') }}</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-700">{{ __('shipments.history_from') }}</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-700">{{ __('shipments.history_to') }}</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-700">{{ __('shipments.history_notes') }}</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-700">{{ __('shipments.history_by') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($shipment->statusHistories as $entry)
                                    <tr class="hover:bg-slate-50/80">
                                        <td class="px-4 py-3 whitespace-nowrap text-slate-600">{{ $entry->created_at->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</td>
                                        <td class="px-4 py-3">{{ $entry->from_status !== null && $entry->from_status === $entry->to_status ? '—' : ($entry->fromStatusLabel() ?? '—') }}</td>
                                        <td class="px-4 py-3 font-medium text-slate-900">{{ $entry->toStatusLabel() }}</td>
                                        <td class="px-4 py-3 text-slate-600">{{ $entry->notes ?? '—' }}</td>
                                        <td class="px-4 py-3 text-slate-600">{{ $entry->changedBy?->name ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <div class="flex justify-start pb-8">
                @if (auth()->user()->isMessenger())
                    <a href="{{ route('courier.shipments.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-800">{{ __('shipments.back_to_list') }}</a>
                @else
                    <a href="{{ route('shipments.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-800">{{ __('shipments.back_to_list') }}</a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
