<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('shipments.edit_shipment') }}
        </h2>
        <p class="text-sm text-gray-600 mt-1">{{ __('shipments.subtitle_edit') }}</p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('shipments.update', $shipment) }}" class="p-6 space-y-8" x-data="shipmentCustomerPicker()">
                    @csrf
                    @method('PUT')
                    @php($s = $shipment)

                    <fieldset class="rounded-2xl border border-slate-200 bg-slate-50/60 p-6 space-y-5 shadow-inner">
                        <legend class="text-base font-bold text-slate-900">{{ __('shipments.customer_section') }}</legend>
                        <div class="grid gap-4 sm:grid-cols-3">
                            <label class="flex gap-3 items-center rounded-xl border border-slate-200 bg-white px-4 py-3 cursor-pointer shadow-sm">
                                <input type="radio" name="customer_mode" value="skip" class="text-brand-600" x-model="mode" />
                                <span class="text-sm font-medium text-slate-800">{{ __('shipments.customer_mode_skip') }}</span>
                            </label>
                            <label class="flex gap-3 items-center rounded-xl border border-slate-200 bg-white px-4 py-3 cursor-pointer shadow-sm">
                                <input type="radio" name="customer_mode" value="existing" class="text-brand-600" x-model="mode" />
                                <span class="text-sm font-medium text-slate-800">{{ __('shipments.customer_mode_existing') }}</span>
                            </label>
                            <label class="flex gap-3 items-center rounded-xl border border-slate-200 bg-white px-4 py-3 cursor-pointer shadow-sm">
                                <input type="radio" name="customer_mode" value="new" class="text-brand-600" x-model="mode" />
                                <span class="text-sm font-medium text-slate-800">{{ __('shipments.customer_mode_new') }}</span>
                            </label>
                        </div>

                        <div x-show="mode === 'existing'" x-cloak class="space-y-4">
                            <p class="text-sm text-slate-600">{{ __('shipments.customer_pick_hint') }}</p>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <input type="search" x-model="search" @input.debounce.300ms="fetchCustomers()" placeholder="{{ __('shipments.customer_search_hint') }}" class="flex-1 rounded-xl border-slate-300 shadow-sm" />
                                <select name="customer_id" id="customer_id_select" x-model="customerId" @change="loadAddresses()" class="rounded-xl border-slate-300 shadow-sm">
                                    <option value="">{{ __('shipments.select_customer') }}</option>
                                    <template x-for="c in results" :key="c.id">
                                        <option :value="c.id" x-text="c.name + ' · ' + c.phone" :selected="String(c.id) === String(customerId)"></option>
                                    </template>
                                </select>
                            </div>
                            <div x-show="addresses.length" class="space-y-2">
                                <x-input-label for="customer_address_id" :value="__('customers.section_addresses')" />
                                <select id="customer_address_id" name="customer_address_id" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm">
                                    <option value="">{{ __('shipments.select_address_optional') }}</option>
                                    <template x-for="a in addresses" :key="a.id">
                                        <option :value="a.id" x-text="a.label + ' — ' + a.address_line"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <div x-show="mode === 'new'" x-cloak class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <x-input-label for="new_customer_name" :value="__('customers.field_name')" />
                                <x-text-input id="new_customer_name" name="new_customer_name" type="text" class="mt-1 block w-full rounded-xl" :value="old('new_customer_name')" />
                                <x-input-error class="mt-2" :messages="$errors->get('new_customer_name')" />
                            </div>
                            <div>
                                <x-input-label for="new_customer_phone" :value="__('customers.field_phone')" />
                                <x-text-input id="new_customer_phone" name="new_customer_phone" type="text" class="mt-1 block w-full rounded-xl" :value="old('new_customer_phone')" />
                                <x-input-error class="mt-2" :messages="$errors->get('new_customer_phone')" />
                            </div>
                            <div>
                                <x-input-label for="new_customer_document" :value="__('customers.field_document')" />
                                <x-text-input id="new_customer_document" name="new_customer_document" type="text" class="mt-1 block w-full rounded-xl" :value="old('new_customer_document')" />
                            </div>
                            <div>
                                <x-input-label for="new_customer_email" :value="__('customers.field_email')" />
                                <x-text-input id="new_customer_email" name="new_customer_email" type="email" class="mt-1 block w-full rounded-xl" :value="old('new_customer_email')" />
                            </div>
                            <div class="sm:col-span-2">
                                <x-input-label for="new_customer_notes" :value="__('customers.field_notes')" />
                                <textarea id="new_customer_notes" name="new_customer_notes" rows="2" class="mt-1 block w-full rounded-xl border-slate-300">{{ old('new_customer_notes') }}</textarea>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="rounded-2xl border border-brand-100 bg-brand-50/40 p-6 space-y-4">
                        <legend class="text-base font-bold text-slate-900">{{ __('shipments.assign_courier') }}</legend>
                        <p class="text-sm text-slate-600">{{ __('shipments.assign_courier_hint') }}</p>
                        <div>
                            <x-input-label for="assigned_user_id" :value="__('shipments.assign_courier')" />
                            <select id="assigned_user_id" name="assigned_user_id" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm @if ($messengers->isEmpty()) opacity-60 cursor-not-allowed @endif" @disabled($messengers->isEmpty())>
                                @if ($messengers->isEmpty())
                                    <option value="">{{ __('shipments.no_messengers_available') }}</option>
                                @else
                                    <option value="">{{ __('shipments.no_courier') }}</option>
                                    @foreach ($messengers as $m)
                                        <option value="{{ $m->id }}" @selected(old('assigned_user_id', $s->assigned_user_id) == $m->id)>{{ $m->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @if ($messengers->isEmpty())
                                <p class="mt-2 text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2">{{ __('shipments.no_messengers_available') }}</p>
                            @endif
                            <x-input-error class="mt-2" :messages="$errors->get('assigned_user_id')" />
                        </div>
                    </fieldset>

                    <fieldset class="space-y-4">
                        <legend class="text-lg font-medium text-gray-900">{{ __('shipments.sender_section') }}</legend>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <x-input-label for="sender_name" :value="__('shipments.sender_name')" />
                                <x-text-input id="sender_name" name="sender_name" type="text" class="mt-1 block w-full" :value="old('sender_name', $s->sender_name)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('sender_name')" />
                            </div>
                            <div>
                                <x-input-label for="sender_phone" :value="__('shipments.sender_phone')" />
                                <x-text-input id="sender_phone" name="sender_phone" type="text" class="mt-1 block w-full" :value="old('sender_phone', $s->sender_phone)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('sender_phone')" />
                            </div>
                            <div class="sm:col-span-2">
                                <x-input-label for="sender_email" :value="__('shipments.sender_email') . ' (' . __('shipments.section_optional') . ')'" />
                                <x-text-input id="sender_email" name="sender_email" type="email" class="mt-1 block w-full" :value="old('sender_email', $s->sender_email)" />
                                <x-input-error class="mt-2" :messages="$errors->get('sender_email')" />
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="space-y-4">
                        <legend class="text-lg font-medium text-gray-900">{{ __('shipments.recipient_section') }}</legend>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <x-input-label for="recipient_name" :value="__('shipments.recipient_name')" />
                                <x-text-input id="recipient_name" name="recipient_name" type="text" class="mt-1 block w-full" :value="old('recipient_name', $s->recipient_name)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('recipient_name')" />
                            </div>
                            <div>
                                <x-input-label for="recipient_phone" :value="__('shipments.recipient_phone')" />
                                <x-text-input id="recipient_phone" name="recipient_phone" type="text" class="mt-1 block w-full" :value="old('recipient_phone', $s->recipient_phone)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('recipient_phone')" />
                            </div>
                            <div class="sm:col-span-2">
                                <x-input-label for="recipient_email" :value="__('shipments.recipient_email') . ' (' . __('shipments.section_optional') . ')'" />
                                <x-text-input id="recipient_email" name="recipient_email" type="email" class="mt-1 block w-full" :value="old('recipient_email', $s->recipient_email)" />
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
                            <textarea id="origin_address_line" name="origin_address_line" rows="2" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('origin_address_line', $s->origin_address_line) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('origin_address_line')" />
                        </div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div data-geo-cascade
                                 data-cities-url="{{ route('geo.cities') }}"
                                 data-old-dept="{{ old('origin_department_id', $s->origin_department_id) }}"
                                 data-old-city="{{ old('origin_city_id', $s->origin_city_id) }}"
                                 class="contents"
                            >
                                <div>
                                    <x-input-label for="origin_department_id" :value="__('shipments.department')" />
                                    <select id="origin_department_id" name="origin_department_id" data-role="department" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">{{ __('shipments.select_department') }}</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}" @selected(old('origin_department_id', $s->origin_department_id) == $department->id)>{{ $department->name }}</option>
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
                                <x-text-input id="origin_postal_code" name="origin_postal_code" type="text" class="mt-1 block w-full" :value="old('origin_postal_code', $s->origin_postal_code)" />
                                <x-input-error class="mt-2" :messages="$errors->get('origin_postal_code')" />
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="space-y-4">
                        <legend class="text-lg font-medium text-gray-900">{{ __('shipments.destination_section') }}</legend>
                        <div>
                            <x-input-label for="destination_address_line" :value="__('shipments.address_line')" />
                            <textarea id="destination_address_line" name="destination_address_line" rows="2" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('destination_address_line', $s->destination_address_line) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('destination_address_line')" />
                        </div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div data-geo-cascade
                                 data-cities-url="{{ route('geo.cities') }}"
                                 data-old-dept="{{ old('destination_department_id', $s->destination_department_id) }}"
                                 data-old-city="{{ old('destination_city_id', $s->destination_city_id) }}"
                                 class="contents"
                            >
                                <div>
                                    <x-input-label for="destination_department_id" :value="__('shipments.department')" />
                                    <select id="destination_department_id" name="destination_department_id" data-role="department" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">{{ __('shipments.select_department') }}</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}" @selected(old('destination_department_id', $s->destination_department_id) == $department->id)>{{ $department->name }}</option>
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
                                <x-text-input id="destination_postal_code" name="destination_postal_code" type="text" class="mt-1 block w-full" :value="old('destination_postal_code', $s->destination_postal_code)" />
                                <x-input-error class="mt-2" :messages="$errors->get('destination_postal_code')" />
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="space-y-4">
                        <legend class="text-lg font-medium text-gray-900">{{ __('shipments.additional_section') }}</legend>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <x-input-label for="reference_internal" :value="__('shipments.reference_internal') . ' (' . __('shipments.section_optional') . ')'" />
                                <x-text-input id="reference_internal" name="reference_internal" type="text" class="mt-1 block w-full" :value="old('reference_internal', $s->reference_internal)" />
                                <x-input-error class="mt-2" :messages="$errors->get('reference_internal')" />
                            </div>
                            <div>
                                <x-input-label for="weight_kg" :value="__('shipments.weight_kg') . ' (' . __('shipments.section_optional') . ')'" />
                                <x-text-input id="weight_kg" name="weight_kg" type="number" step="0.001" min="0" class="mt-1 block w-full" :value="old('weight_kg', $s->weight_kg)" />
                                <x-input-error class="mt-2" :messages="$errors->get('weight_kg')" />
                            </div>
                            <div>
                                <x-input-label for="declared_value" :value="__('shipments.declared_value') . ' (' . __('shipments.section_optional') . ')'" />
                                <x-text-input id="declared_value" name="declared_value" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('declared_value', $s->declared_value)" />
                                <x-input-error class="mt-2" :messages="$errors->get('declared_value')" />
                            </div>
                            <div class="sm:col-span-2">
                                <x-input-label for="notes_internal" :value="__('shipments.notes_internal') . ' (' . __('shipments.section_optional') . ')'" />
                                <textarea id="notes_internal" name="notes_internal" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes_internal', $s->notes_internal) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('notes_internal')" />
                            </div>
                        </div>
                    </fieldset>

                    <div class="flex items-center justify-end gap-4">
                        <a href="{{ route('shipments.index') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('shipments.cancel') }}</a>
                        <x-primary-button>{{ __('shipments.save_changes') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function shipmentCustomerPicker() {
                return {
                    mode: @json(old('customer_mode', $s->customer_id ? 'existing' : 'skip')),
                    search: '',
                    customerId: @json(old('customer_id') !== null ? (string) old('customer_id') : ($s->customer_id ? (string) $s->customer_id : '')),
                    results: @json($initialCustomers ?? []),
                    addresses: [],
                    async fetchCustomers() {
                        const url = @json(route('customers.search')) + '?q=' + encodeURIComponent(this.search);
                        const res = await fetch(url, { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                        const data = await res.json();
                        this.results = data.data ?? [];
                    },
                    async loadAddresses() {
                        if (!this.customerId) {
                            this.addresses = [];
                            return;
                        }
                        const res = await fetch(`{{ url('/customers') }}/${this.customerId}/addresses`, {
                            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        });
                        const data = await res.json();
                        this.addresses = data.data ?? [];
                    },
                    init() {
                        if (this.mode === 'existing' && this.customerId) {
                            this.loadAddresses();
                        }
                    },
                };
            }

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
