<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-display text-2xl md:text-3xl font-bold text-slate-900">Reporte de Mensajeros</h2>
            <p class="mt-1 text-sm text-slate-600">Operación y desempeño financiero por mensajero</p>
        </div>
    </x-slot>

    <div class="py-10 md:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8 px-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl">
                    <p class="text-sm text-slate-500">Total ingresos (entregados)</p>
                    <p class="mt-2 text-3xl font-bold text-emerald-700">${{ number_format($globalMetrics['total_income'], 2, ',', '.') }}</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl">
                    <p class="text-sm text-slate-500">Total pendiente de cobro</p>
                    <p class="mt-2 text-3xl font-bold text-amber-700">${{ number_format($globalMetrics['total_pending_collection'], 2, ',', '.') }}</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl">
                    <p class="text-sm text-slate-500">Total envíos filtrados</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $globalMetrics['total_shipments'] }}</p>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl">
                <form method="GET" action="{{ route('operations.messengers.report') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <input type="date" name="from" value="{{ request('from') }}" class="rounded-xl border-slate-300" />
                    <input type="date" name="to" value="{{ request('to') }}" class="rounded-xl border-slate-300" />
                    <select name="status" class="rounded-xl border-slate-300">
                        <option value="">Estado envío</option>
                        @foreach (\App\Shipments\ShipmentStatus::all() as $st)
                            <option value="{{ $st }}" @selected(request('status') === $st)>{{ \App\Shipments\ShipmentStatus::label($st) }}</option>
                        @endforeach
                    </select>
                    <select name="payment_status" class="rounded-xl border-slate-300">
                        <option value="">Estado pago</option>
                        @foreach (\App\Finance\PaymentStatus::all() as $ps)
                            <option value="{{ $ps }}" @selected(request('payment_status') === $ps)>{{ \App\Finance\PaymentStatus::label($ps) }}</option>
                        @endforeach
                    </select>
                    <select name="customer_id" class="rounded-xl border-slate-300">
                        <option value="">Cliente</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" @selected((string) request('customer_id') === (string) $customer->id)>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                    <select name="messenger_id" class="rounded-xl border-slate-300">
                        <option value="">Mensajero</option>
                        @foreach ($summaryRows as $row)
                            <option value="{{ $row['id'] }}" @selected((int) request('messenger_id', $selectedMessengerId) === (int) $row['id'])>{{ $row['name'] }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por guía, cliente o mensajero" class="rounded-xl border-slate-300 md:col-span-2" />
                    <div class="md:col-span-4 flex flex-wrap gap-2">
                        <button type="submit" class="inline-flex rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white">{{ __('shipments.filter_apply') }}</button>
                        <a href="{{ route('operations.messengers.report') }}" class="inline-flex rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">{{ __('shipments.filter_clear') }}</a>
                    </div>
                </form>
                <div class="flex justify-end items-center gap-2 mt-4 mb-3">
                    <a href="{{ route('operations.messengers.report.pdf', request()->query()) }}"
                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg shadow hover:bg-red-700 transition">
                        Exportar PDF
                    </a>

                    <a href="{{ route('operations.messengers.report.excel', request()->query()) }}"
                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg shadow hover:bg-green-700 transition">
                        Exportar Excel
                    </a>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl overflow-x-auto">
                <h3 class="font-bold text-slate-900 mb-4">Listado general</h3>
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left">Nombre</th>
                            <th class="px-4 py-3 text-left">Estado</th>
                            <th class="px-4 py-3 text-right">Total envíos</th>
                            <th class="px-4 py-3 text-right">Entregados</th>
                            <th class="px-4 py-3 text-right">Pendientes</th>
                            <th class="px-4 py-3 text-right">Ingresos</th>
                            <th class="px-4 py-3 text-right">Pendiente cobro</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($summaryRows as $row)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-slate-900">{{ $row['name'] }}</td>
                                <td class="px-4 py-3">{{ $row['active'] ? 'Activo' : 'Inactivo' }}</td>
                                <td class="px-4 py-3 text-right">{{ $row['total_shipments'] }}</td>
                                <td class="px-4 py-3 text-right">{{ $row['delivered'] }}</td>
                                <td class="px-4 py-3 text-right">{{ $row['pending'] }}</td>
                                <td class="px-4 py-3 text-right">${{ number_format($row['income_generated'], 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right">${{ number_format($row['pending_collection'], 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($detailMetrics)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl">
                        <h3 class="font-bold text-slate-900 mb-4">Detalle por mensajero</h3>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>Total envíos: <strong>{{ $detailMetrics['total'] }}</strong></div>
                            <div>Entregados: <strong>{{ $detailMetrics['delivered'] }}</strong></div>
                            <div>En ruta: <strong>{{ $detailMetrics['in_route'] }}</strong></div>
                            <div>Pendientes: <strong>{{ $detailMetrics['pending'] }}</strong></div>
                            <div>Cancelados: <strong>{{ $detailMetrics['cancelled'] }}</strong></div>
                            <div>Ingresos: <strong>${{ number_format($detailMetrics['income_generated'], 2, ',', '.') }}</strong></div>
                            <div class="col-span-2">Pendiente cobro: <strong>${{ number_format($detailMetrics['pending_collection'], 2, ',', '.') }}</strong></div>
                        </div>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl">
                        <h3 class="font-bold text-slate-900 mb-3">Envíos por estado</h3>
                        <canvas id="chartStatus" height="160"></canvas>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl">
                        <h3 class="font-bold text-slate-900 mb-3">Ingresos por fecha</h3>
                        <canvas id="chartIncome" height="160"></canvas>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl">
                        <h3 class="font-bold text-slate-900 mb-3">Evolución de entregas</h3>
                        <canvas id="chartDeliveries" height="160"></canvas>
                    </div>
                </div>
            @endif

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl overflow-x-auto">
                <h3 class="font-bold text-slate-900 mb-4">Historial</h3>
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left">Fecha</th>
                            <th class="px-4 py-3 text-left">Guía</th>
                            <th class="px-4 py-3 text-left">Cliente</th>
                            <th class="px-4 py-3 text-left">Estado</th>
                            <th class="px-4 py-3 text-right">Valor</th>
                            <th class="px-4 py-3 text-left">Estado pago</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($history as $shipment)
                            <tr>
                                <td class="px-4 py-3">{{ $shipment->created_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3 font-mono">{{ $shipment->tracking_number }}</td>
                                <td class="px-4 py-3">{{ $shipment->customer?->name ?? __('finance.unknown_customer_group') }}</td>
                                <td class="px-4 py-3">{{ \App\Shipments\ShipmentStatus::label($shipment->status) }}</td>
                                <td class="px-4 py-3 text-right">${{ number_format((float) ($shipment->cost ?? 0), 2, ',', '.') }}</td>
                                <td class="px-4 py-3">{{ \App\Finance\PaymentStatus::label($shipment->payment_status ?? \App\Finance\PaymentStatus::PENDING) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-slate-600">{{ __('shipments.empty') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $history->links() }}</div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
        <script defer>
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof Chart === 'undefined') return;
                const statusEl = document.getElementById('chartStatus');
                if (statusEl) {
                    new Chart(statusEl, { type: 'pie', data: { labels: @json($statusChart['labels']), datasets: [{ data: @json($statusChart['data']) }] } });
                }
                const incomeEl = document.getElementById('chartIncome');
                if (incomeEl) {
                    new Chart(incomeEl, { type: 'line', data: { labels: @json($incomeChart['labels']), datasets: [{ label: 'Ingresos', data: @json($incomeChart['data']), borderColor: '#2563eb' }] } });
                }
                const deliveryEl = document.getElementById('chartDeliveries');
                if (deliveryEl) {
                    new Chart(deliveryEl, { type: 'bar', data: { labels: @json($deliveriesChart['labels']), datasets: [{ label: 'Entregas', data: @json($deliveriesChart['data']), backgroundColor: '#10b981' }] } });
                }
            });
        </script>
    @endpush
</x-app-layout>
