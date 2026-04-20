<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-display text-2xl md:text-3xl font-bold text-slate-900 tracking-tight">{{ __('dashboard.title') }}</h2>
            @isset($currentOrganization)
                <p class="mt-1 text-sm text-slate-600">
                    {{ __('dashboard.active_organization') }}
                    <span class="font-semibold text-brand-800">{{ $currentOrganization->name }}</span>
                </p>
            @endisset
        </div>
    </x-slot>

    <div class="py-10 md:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8 px-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="rounded-3xl border border-slate-200/90 bg-white p-6 shadow-xl shadow-slate-900/10 ring-1 ring-slate-900/5">
                    <p class="text-sm font-medium text-slate-500">{{ __('dashboard.metric_shipments_today') }}</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $metrics['registered_today'] }}</p>
                    <p class="mt-1 text-xs text-slate-400">{{ __('dashboard.metric_shipments_today_hint') }}</p>
                </div>
                <div class="rounded-3xl border border-slate-200/90 bg-white p-6 shadow-xl shadow-slate-900/10 ring-1 ring-slate-900/5">
                    <p class="text-sm font-medium text-slate-500">{{ __('dashboard.metric_in_transit') }}</p>
                    <p class="mt-2 text-3xl font-bold text-brand-800">{{ $metrics['in_transit'] }}</p>
                    <p class="mt-1 text-xs text-slate-400">{{ __('dashboard.metric_in_transit_hint') }}</p>
                </div>
                <div class="rounded-3xl border border-slate-200/90 bg-white p-6 shadow-xl shadow-slate-900/10 ring-1 ring-slate-900/5">
                    <p class="text-sm font-medium text-slate-500">{{ __('dashboard.metric_delivered_total') }}</p>
                    <p class="mt-2 text-3xl font-bold text-emerald-700">{{ $metrics['delivered_total'] }}</p>
                    <p class="mt-1 text-xs text-slate-400">{{ __('dashboard.metric_delivered_total_hint') }}</p>
                </div>
                <div class="rounded-3xl border border-slate-200/90 bg-white p-6 shadow-xl shadow-slate-900/10 ring-1 ring-slate-900/5">
                    <p class="text-sm font-medium text-slate-500">{{ __('dashboard.metric_incidents') }}</p>
                    <p class="mt-2 text-3xl font-bold text-red-700">{{ $metrics['incidents'] }}</p>
                    <p class="mt-1 text-xs text-slate-400">{{ __('dashboard.metric_incidents_hint') }}</p>
                </div>
            </div>

            @if ($financialMetrics !== null)
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="rounded-3xl border border-emerald-200/80 bg-gradient-to-br from-emerald-50/90 to-white p-6 shadow-xl shadow-slate-900/10 ring-1 ring-emerald-900/5">
                        <p class="text-sm font-medium text-slate-600">{{ __('finance.dashboard_billed_month') }}</p>
                        <p class="mt-2 text-2xl font-bold text-emerald-800">${{ number_format($financialMetrics['billed_month'], 2, ',', '.') }}</p>
                    </div>
                    <div class="rounded-3xl border border-sky-200/80 bg-gradient-to-br from-sky-50/90 to-white p-6 shadow-xl shadow-slate-900/10 ring-1 ring-sky-900/5">
                        <p class="text-sm font-medium text-slate-600">{{ __('finance.dashboard_paid_month') }}</p>
                        <p class="mt-2 text-2xl font-bold text-sky-800">${{ number_format($financialMetrics['paid_month'], 2, ',', '.') }}</p>
                    </div>
                    <div class="rounded-3xl border border-amber-200/80 bg-gradient-to-br from-amber-50/90 to-white p-6 shadow-xl shadow-slate-900/10 ring-1 ring-amber-900/5">
                        <p class="text-sm font-medium text-slate-600">{{ __('finance.dashboard_pending_balance') }}</p>
                        <p class="mt-2 text-2xl font-bold text-amber-900">${{ number_format($financialMetrics['pending_balance'], 2, ',', '.') }}</p>
                    </div>
                </div>
            @endif

            <div class="rounded-3xl border border-slate-200/90 bg-white p-6 md:p-8 shadow-xl shadow-slate-900/10 ring-1 ring-slate-900/5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div>
                        <h3 class="font-display text-lg font-bold text-slate-900">{{ __('dashboard.chart_title') }}</h3>
                        <p class="text-sm text-slate-600 mt-1">{{ __('dashboard.chart_subtitle') }}</p>
                    </div>
                </div>
                <div class="h-72 w-full">
                    <canvas id="dashboardShipmentsChart" aria-label="{{ __('dashboard.chart_title') }}"></canvas>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200/90 bg-gradient-to-br from-white to-brand-50/40 p-8 shadow-xl shadow-slate-900/10 ring-1 ring-slate-900/5">
                <p class="text-sm text-slate-700">{{ __('dashboard.hint_shipments') }}</p>
                @if ($courierDashboard)
                    <a href="{{ route('courier.shipments.index') }}" class="mt-4 inline-flex items-center font-semibold text-brand-600 hover:text-brand-800">{{ __('shipments.my_shipments_menu') }} →</a>
                @else
                    <a href="{{ route('shipments.index') }}" class="mt-4 inline-flex items-center font-semibold text-brand-600 hover:text-brand-800">{{ __('shipments.menu') }} →</a>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
        <script defer>
            document.addEventListener('DOMContentLoaded', function () {
                const el = document.getElementById('dashboardShipmentsChart');
                if (!el || typeof Chart === 'undefined') return;

                const labels = @json($chartDailyLabels);
                const data = @json($chartDailyCounts);

                new Chart(el, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: @json(__('dashboard.chart_series_label')),
                            data: data,
                            backgroundColor: 'rgba(37, 99, 235, 0.55)',
                            borderColor: 'rgba(29, 78, 216, 1)',
                            borderWidth: 1,
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1, precision: 0 }
                            }
                        },
                        plugins: {
                            legend: { display: true, position: 'bottom' }
                        }
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
