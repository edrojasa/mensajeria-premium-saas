<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-display text-2xl md:text-3xl font-bold text-slate-900">{{ __('finance.reports_title') }}</h2>
            <p class="mt-1 text-sm text-slate-600">{{ __('finance.reports_subtitle') }}</p>
        </div>
    </x-slot>

    <div class="py-10 md:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8 px-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="rounded-3xl border border-slate-200/90 bg-white p-6 shadow-xl ring-1 ring-slate-900/5">
                    <p class="text-sm font-medium text-slate-500">{{ __('finance.kpi_billed_month') }}</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">${{ number_format($billedMonth, 2, ',', '.') }}</p>
                    <p class="mt-1 text-xs text-slate-400">{{ __('finance.kpi_billed_hint') }}</p>
                </div>
                <div class="rounded-3xl border border-slate-200/90 bg-white p-6 shadow-xl ring-1 ring-slate-900/5">
                    <p class="text-sm font-medium text-slate-500">{{ __('finance.kpi_paid_month') }}</p>
                    <p class="mt-2 text-3xl font-bold text-emerald-700">${{ number_format($paidMonth, 2, ',', '.') }}</p>
                    <p class="mt-1 text-xs text-slate-400">{{ __('finance.kpi_paid_hint') }}</p>
                </div>
                <div class="rounded-3xl border border-slate-200/90 bg-white p-6 shadow-xl ring-1 ring-slate-900/5">
                    <p class="text-sm font-medium text-slate-500">{{ __('finance.kpi_pending') }}</p>
                    <p class="mt-2 text-3xl font-bold text-amber-700">${{ number_format($pendingPortfolio, 2, ',', '.') }}</p>
                    <p class="mt-1 text-xs text-slate-400">{{ __('finance.kpi_pending_hint') }}</p>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200/90 bg-white p-6 md:p-8 shadow-xl ring-1 ring-slate-900/5">
                <h3 class="font-display text-lg font-bold text-slate-900">{{ __('finance.chart_title') }}</h3>
                <p class="text-sm text-slate-600 mt-1">{{ __('finance.chart_subtitle') }}</p>
                <div class="h-72 w-full mt-6">
                    <canvas id="financialTrendChart"></canvas>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200/90 bg-white p-6 md:p-8 shadow-xl ring-1 ring-slate-900/5">
                <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                    <div>
                        <h3 class="font-display text-lg font-bold text-slate-900">{{ __('finance.movements_title') }}</h3>
                        <p class="text-sm text-slate-600">{{ __('finance.movements_subtitle') }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('financial.movements.pdf', request()->query()) }}" class="inline-flex rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">{{ __('exports.pdf') }}</a>
                        <a href="{{ route('financial.movements.excel', request()->query()) }}" class="inline-flex rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">{{ __('exports.excel') }}</a>
                    </div>
                </div>

                <form method="GET" action="{{ route('financial.reports') }}" class="mt-5 grid grid-cols-1 md:grid-cols-4 gap-3">
                    <input type="date" name="from" value="{{ request('from') }}" class="rounded-xl border-slate-300" />
                    <input type="date" name="to" value="{{ request('to') }}" class="rounded-xl border-slate-300" />
                    <select name="status" class="rounded-xl border-slate-300">
                        <option value="">{{ __('finance.filter_status') }}</option>
                        <option value="pending" @selected(request('status') === 'pending')>{{ __('finance.payment_statuses.pending') }}</option>
                        <option value="paid" @selected(request('status') === 'paid')>{{ __('finance.payment_statuses.paid') }}</option>
                    </select>
                    <select name="type" class="rounded-xl border-slate-300">
                        <option value="">{{ __('finance.filter_type') }}</option>
                        <option value="cargo" @selected(request('type') === 'cargo')>{{ __('finance.type_charge') }}</option>
                        <option value="pago" @selected(request('type') === 'pago')>{{ __('finance.type_payment') }}</option>
                    </select>
                    <select name="customer_id" class="rounded-xl border-slate-300">
                        <option value="">{{ __('finance.filter_customer') }}</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" @selected((string) request('customer_id') === (string) $customer->id)>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                    <select name="user_id" class="rounded-xl border-slate-300">
                        <option value="">{{ __('finance.filter_user') }}</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected((string) request('user_id') === (string) $user->id)>{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('finance.filter_search') }}" class="rounded-xl border-slate-300 md:col-span-2" />
                    <div class="md:col-span-4 flex gap-2">
                        <button type="submit" class="inline-flex rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white">{{ __('shipments.filter_apply') }}</button>
                        <a href="{{ route('financial.reports') }}" class="inline-flex rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">{{ __('shipments.filter_clear') }}</a>
                    </div>
                </form>

                <div class="mt-5 overflow-x-auto rounded-2xl border border-slate-200">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left">Fecha</th>
                                <th class="px-4 py-3 text-left">Cliente</th>
                                <th class="px-4 py-3 text-left">Envío</th>
                                <th class="px-4 py-3 text-right">Valor</th>
                                <th class="px-4 py-3 text-left">Estado</th>
                                <th class="px-4 py-3 text-left">Usuario</th>
                                <th class="px-4 py-3 text-left">Tipo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($movements as $movement)
                                <tr>
                                    <td class="px-4 py-3">{{ optional($movement['date'])->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3">{{ $movement['customer'] }}</td>
                                    <td class="px-4 py-3 font-mono"><a href="{{ route('shipments.show', $movement['shipment']) }}" class="text-brand-700">{{ $movement['tracking'] }}</a></td>
                                    <td class="px-4 py-3 text-right">${{ number_format((float) $movement['value'], 2, ',', '.') }}</td>
                                    <td class="px-4 py-3">{{ \App\Finance\PaymentStatus::label($movement['status']) }}</td>
                                    <td class="px-4 py-3">{{ $movement['user'] }}</td>
                                    <td class="px-4 py-3">{{ $movement['type'] === 'pago' ? __('finance.type_payment') : __('finance.type_charge') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-4 py-8 text-center text-slate-600">{{ __('shipments.empty') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $movements->links() }}</div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
        <script defer>
            document.addEventListener('DOMContentLoaded', function () {
                const el = document.getElementById('financialTrendChart');
                if (!el || typeof Chart === 'undefined') return;
                new Chart(el, {
                    type: 'bar',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [
                            {
                                label: @json(__('finance.chart_billed')),
                                data: @json($chartBilled),
                                backgroundColor: 'rgba(37, 99, 235, 0.5)',
                                borderColor: 'rgba(29, 78, 216, 1)',
                                borderWidth: 1,
                            },
                            {
                                label: @json(__('finance.chart_paid')),
                                data: @json($chartPaid),
                                backgroundColor: 'rgba(16, 185, 129, 0.45)',
                                borderColor: 'rgba(5, 150, 105, 1)',
                                borderWidth: 1,
                            },
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true }
                        },
                        plugins: { legend: { position: 'bottom' } }
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
