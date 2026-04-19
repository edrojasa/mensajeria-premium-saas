<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('dashboard.title') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @isset($currentOrganization)
                <p class="text-sm text-gray-600 px-1 sm:px-0">
                    {{ __('dashboard.active_organization') }}
                    <span class="font-medium text-gray-900">{{ $currentOrganization->name }}</span>
                </p>
            @endisset

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">{{ __('dashboard.metric_registered_today') }}</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $metrics['registered_today'] }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">{{ __('dashboard.metric_in_transit') }}</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $metrics['in_transit'] }}</p>
                    <p class="mt-1 text-xs text-gray-400">{{ __('dashboard.metric_in_transit_hint') }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">{{ __('dashboard.metric_delivered_today') }}</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $metrics['delivered_today'] }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">{{ __('dashboard.metric_incidents') }}</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $metrics['incidents'] }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-700">{{ __('dashboard.hint_shipments') }}</p>
                <a href="{{ route('shipments.index') }}" class="mt-3 inline-flex text-indigo-600 hover:text-indigo-900 text-sm font-medium">{{ __('shipments.menu') }} →</a>
            </div>
        </div>
    </div>
</x-app-layout>
