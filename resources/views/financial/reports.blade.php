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
