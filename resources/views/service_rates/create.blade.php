@php
    use App\Finance\ServiceType;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-2xl font-bold text-slate-900">{{ __('finance.rate_create') }}</h2>
    </x-slot>

    <div class="py-10 md:py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 px-4">
            <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-xl ring-1 ring-slate-900/5">
                @if (empty($availableTypes))
                    <p class="text-slate-600">{{ __('finance.rate_empty') }}</p>
                    <a href="{{ route('service-rates.index') }}" class="mt-4 inline-block font-semibold text-brand-600">{{ __('shipments.back_to_list') }}</a>
                @else
                    <form method="POST" action="{{ route('service-rates.store') }}" class="space-y-6">
                        @csrf
                        <div>
                            <x-input-label for="service_type" :value="__('finance.field_service_type')" />
                            <select id="service_type" name="service_type" required class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm">
                                @foreach ($availableTypes as $st)
                                    <option value="{{ $st }}">{{ ServiceType::label($st) }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('service_type')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="base_price" :value="__('finance.field_base_price')" />
                            <x-text-input id="base_price" name="base_price" type="number" step="0.01" min="0" required class="mt-1 block w-full rounded-xl" :value="old('base_price')" />
                            <x-input-error :messages="$errors->get('base_price')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="price_per_kg" :value="__('finance.field_price_per_kg') . ' (' . __('shipments.section_optional') . ')'" />
                            <x-text-input id="price_per_kg" name="price_per_kg" type="number" step="0.0001" min="0" class="mt-1 block w-full rounded-xl" :value="old('price_per_kg')" />
                            <x-input-error :messages="$errors->get('price_per_kg')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="price_per_km" :value="__('finance.field_price_per_km') . ' (' . __('shipments.section_optional') . ')'" />
                            <x-text-input id="price_per_km" name="price_per_km" type="number" step="0.0001" min="0" class="mt-1 block w-full rounded-xl" :value="old('price_per_km')" />
                            <x-input-error :messages="$errors->get('price_per_km')" class="mt-2" />
                        </div>
                        <div class="flex items-center gap-2">
                            <input id="active" name="active" type="checkbox" value="1" class="rounded border-slate-300 text-brand-600" @checked(old('active', true)) />
                            <x-input-label for="active" :value="__('finance.field_active')" />
                        </div>
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('service-rates.index') }}" class="text-sm text-slate-600 hover:text-slate-900">{{ __('shipments.cancel') }}</a>
                            <x-primary-button class="rounded-2xl">{{ __('finance.rate_create') }}</x-primary-button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
