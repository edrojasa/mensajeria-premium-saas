<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('shipments.create_action') }}
        </h2>
        <p class="text-sm text-gray-600 mt-1">{{ __('shipments.subtitle_create') }}</p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('shipments.store') }}" class="p-6 space-y-8">
                    @csrf

                    <fieldset class="space-y-4">
                        <legend class="text-lg font-medium text-gray-900">{{ __('shipments.sender_section') }}</legend>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <x-input-label for="sender_name" :value="__('shipments.sender_name')" />
                                <x-text-input id="sender_name" name="sender_name" type="text" class="mt-1 block w-full" :value="old('sender_name')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('sender_name')" />
                            </div>
                            <div>
                                <x-input-label for="sender_phone" :value="__('shipments.sender_phone')" />
                                <x-text-input id="sender_phone" name="sender_phone" type="text" class="mt-1 block w-full" :value="old('sender_phone')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('sender_phone')" />
                            </div>
                            <div class="sm:col-span-2">
                                <x-input-label for="sender_email" :value="__('shipments.sender_email') . ' (' . __('shipments.section_optional') . ')'" />
                                <x-text-input id="sender_email" name="sender_email" type="email" class="mt-1 block w-full" :value="old('sender_email')" />
                                <x-input-error class="mt-2" :messages="$errors->get('sender_email')" />
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="space-y-4">
                        <legend class="text-lg font-medium text-gray-900">{{ __('shipments.recipient_section') }}</legend>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <x-input-label for="recipient_name" :value="__('shipments.recipient_name')" />
                                <x-text-input id="recipient_name" name="recipient_name" type="text" class="mt-1 block w-full" :value="old('recipient_name')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('recipient_name')" />
                            </div>
                            <div>
                                <x-input-label for="recipient_phone" :value="__('shipments.recipient_phone')" />
                                <x-text-input id="recipient_phone" name="recipient_phone" type="text" class="mt-1 block w-full" :value="old('recipient_phone')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('recipient_phone')" />
                            </div>
                            <div class="sm:col-span-2">
                                <x-input-label for="recipient_email" :value="__('shipments.recipient_email') . ' (' . __('shipments.section_optional') . ')'" />
                                <x-text-input id="recipient_email" name="recipient_email" type="email" class="mt-1 block w-full" :value="old('recipient_email')" />
                                <x-input-error class="mt-2" :messages="$errors->get('recipient_email')" />
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="space-y-4">
                        <legend class="text-lg font-medium text-gray-900">{{ __('shipments.origin_section') }}</legend>
                        @if ($departments->isEmpty())
                            <p class="text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-md p-3">{{ __('shipments.departments_missing_hint') }}</p>
                        @endif
                        <div>
                            <x-input-label for="origin_address_line" :value="__('shipments.address_line')" />
                            <textarea id="origin_address_line" name="origin_address_line" rows="2" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('origin_address_line') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('origin_address_line')" />
                        </div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div data-geo-cascade
                                 data-cities-url="{{ route('geo.cities') }}"
                                 data-old-dept="{{ old('origin_department_id') }}"
                                 data-old-city="{{ old('origin_city_id') }}"
                                 class="contents"
                            >
                                <div>
                                    <x-input-label for="origin_department_id" :value="__('shipments.department')" />
                                    <select id="origin_department_id" name="origin_department_id" data-role="department" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">{{ __('shipments.select_department') }}</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}" @selected(old('origin_department_id') == $department->id)>{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('origin_department_id')" />
                                </div>
                                <div>
                                    <x-input-label for="origin_city_id" :value="__('shipments.city')" />
                                    <select id="origin_city_id" name="origin_city_id" data-role="city" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">{{ __('shipments.select_city') }}</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('origin_city_id')" />
                                </div>
                            </div>
                            <div>
                                <x-input-label for="origin_postal_code" :value="__('shipments.postal_code') . ' (' . __('shipments.section_optional') . ')'" />
                                <x-text-input id="origin_postal_code" name="origin_postal_code" type="text" class="mt-1 block w-full" :value="old('origin_postal_code')" />
                                <x-input-error class="mt-2" :messages="$errors->get('origin_postal_code')" />
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="space-y-4">
                        <legend class="text-lg font-medium text-gray-900">{{ __('shipments.destination_section') }}</legend>
                        <div>
                            <x-input-label for="destination_address_line" :value="__('shipments.address_line')" />
                            <textarea id="destination_address_line" name="destination_address_line" rows="2" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('destination_address_line') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('destination_address_line')" />
                        </div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div data-geo-cascade
                                 data-cities-url="{{ route('geo.cities') }}"
                                 data-old-dept="{{ old('destination_department_id') }}"
                                 data-old-city="{{ old('destination_city_id') }}"
                                 class="contents"
                            >
                                <div>
                                    <x-input-label for="destination_department_id" :value="__('shipments.department')" />
                                    <select id="destination_department_id" name="destination_department_id" data-role="department" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">{{ __('shipments.select_department') }}</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}" @selected(old('destination_department_id') == $department->id)>{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('destination_department_id')" />
                                </div>
                                <div>
                                    <x-input-label for="destination_city_id" :value="__('shipments.city')" />
                                    <select id="destination_city_id" name="destination_city_id" data-role="city" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">{{ __('shipments.select_city') }}</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('destination_city_id')" />
                                </div>
                            </div>
                            <div>
                                <x-input-label for="destination_postal_code" :value="__('shipments.postal_code') . ' (' . __('shipments.section_optional') . ')'" />
                                <x-text-input id="destination_postal_code" name="destination_postal_code" type="text" class="mt-1 block w-full" :value="old('destination_postal_code')" />
                                <x-input-error class="mt-2" :messages="$errors->get('destination_postal_code')" />
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="space-y-4">
                        <legend class="text-lg font-medium text-gray-900">{{ __('shipments.additional_section') }}</legend>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <x-input-label for="reference_internal" :value="__('shipments.reference_internal') . ' (' . __('shipments.section_optional') . ')'" />
                                <x-text-input id="reference_internal" name="reference_internal" type="text" class="mt-1 block w-full" :value="old('reference_internal')" />
                                <x-input-error class="mt-2" :messages="$errors->get('reference_internal')" />
                            </div>
                            <div>
                                <x-input-label for="weight_kg" :value="__('shipments.weight_kg') . ' (' . __('shipments.section_optional') . ')'" />
                                <x-text-input id="weight_kg" name="weight_kg" type="number" step="0.001" min="0" class="mt-1 block w-full" :value="old('weight_kg')" />
                                <x-input-error class="mt-2" :messages="$errors->get('weight_kg')" />
                            </div>
                            <div>
                                <x-input-label for="declared_value" :value="__('shipments.declared_value') . ' (' . __('shipments.section_optional') . ')'" />
                                <x-text-input id="declared_value" name="declared_value" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('declared_value')" />
                                <x-input-error class="mt-2" :messages="$errors->get('declared_value')" />
                            </div>
                            <div class="sm:col-span-2">
                                <x-input-label for="notes_internal" :value="__('shipments.notes_internal') . ' (' . __('shipments.section_optional') . ')'" />
                                <textarea id="notes_internal" name="notes_internal" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes_internal') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('notes_internal')" />
                            </div>
                        </div>
                    </fieldset>

                    <div class="flex items-center justify-end gap-4">
                        <a href="{{ route('shipments.index') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('shipments.cancel') }}</a>
                        <x-primary-button>{{ __('shipments.create_button') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const selectCityLabel = @json(__('shipments.select_city'));

                document.querySelectorAll('[data-geo-cascade]').forEach((root) => {
                    const url = root.dataset.citiesUrl;
                    const dept = root.querySelector('[data-role="department"]');
                    const city = root.querySelector('[data-role="city"]');

                    async function loadCities() {
                        const departmentId = dept.value;
                        city.innerHTML = '';
                        const placeholder = document.createElement('option');
                        placeholder.value = '';
                        placeholder.textContent = selectCityLabel;
                        city.appendChild(placeholder);

                        if (!departmentId) {
                            return;
                        }

                        const res = await fetch(
                            url + '?department_id=' + encodeURIComponent(departmentId),
                            {
                                headers: {
                                    Accept: 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            }
                        );
                        const rows = await res.json();

                        rows.forEach((row) => {
                            const opt = document.createElement('option');
                            opt.value = row.id;
                            opt.textContent = row.name;
                            city.appendChild(opt);
                        });

                        const oldCity = root.dataset.oldCity;
                        if (oldCity) {
                            city.value = oldCity;
                        }
                    }

                    dept.addEventListener('change', () => {
                        root.dataset.oldCity = '';
                        loadCities();
                    });

                    if (dept.value) {
                        loadCities();
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
