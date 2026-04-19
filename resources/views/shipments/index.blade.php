<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('shipments.title') }}
            </h2>
            <a href="{{ route('shipments.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                {{ __('shipments.create_action') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($shipments->isEmpty())
                        <p class="text-gray-600">{{ __('shipments.empty') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-4 py-2 text-left font-medium text-gray-700">{{ __('shipments.tracking_number') }}</th>
                                        <th scope="col" class="px-4 py-2 text-left font-medium text-gray-700">{{ __('shipments.recipient_name') }}</th>
                                        <th scope="col" class="px-4 py-2 text-left font-medium text-gray-700">{{ __('shipments.destination_city') }}</th>
                                        <th scope="col" class="px-4 py-2 text-left font-medium text-gray-700">{{ __('shipments.current_status') }}</th>
                                        <th scope="col" class="px-4 py-2 text-left font-medium text-gray-700">{{ __('shipments.created_at_column') }}</th>
                                        <th scope="col" class="px-4 py-2"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($shipments as $shipment)
                                        <tr>
                                            <td class="px-4 py-2 font-mono">{{ $shipment->tracking_number }}</td>
                                            <td class="px-4 py-2">{{ $shipment->recipient_name }}</td>
                                            <td class="px-4 py-2">{{ $shipment->destination_city }}</td>
                                            <td class="px-4 py-2">{{ $shipment->statusLabel() }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap">{{ $shipment->created_at->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</td>
                                            <td class="px-4 py-2 text-right">
                                                <a href="{{ route('shipments.show', $shipment) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('shipments.view_detail') }}</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $shipments->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
