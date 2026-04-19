<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-2xl font-bold text-slate-900">{{ __('customers.edit_action') }}</h2>
    </x-slot>

    <div class="py-10 md:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('customers.update', $customer) }}" class="rounded-3xl border border-slate-200 bg-white shadow-2xl shadow-slate-900/10 p-8 space-y-8 ring-1 ring-slate-900/5">
                @csrf
                @method('PUT')
                @php($c = $customer)
                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-input-label for="name" :value="__('customers.field_name')" />
                        <x-text-input id="name" name="name" class="mt-2 block w-full rounded-2xl" :value="old('name', $c->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="document" :value="__('customers.field_document')" />
                        <x-text-input id="document" name="document" class="mt-2 block w-full rounded-2xl" :value="old('document', $c->document)" />
                    </div>
                    <div>
                        <x-input-label for="phone" :value="__('customers.field_phone')" />
                        <x-text-input id="phone" name="phone" class="mt-2 block w-full rounded-2xl" :value="old('phone', $c->phone)" required />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="email" :value="__('customers.field_email')" />
                        <x-text-input id="email" name="email" type="email" class="mt-2 block w-full rounded-2xl" :value="old('email', $c->email)" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="notes" :value="__('customers.field_notes')" />
                        <textarea id="notes" name="notes" rows="3" class="mt-2 block w-full rounded-2xl border-slate-300">{{ old('notes', $c->notes) }}</textarea>
                    </div>
                </div>

                <fieldset class="space-y-4 rounded-2xl border border-slate-100 bg-slate-50/80 p-6">
                    <legend class="text-sm font-bold text-slate-900">{{ __('customers.section_addresses') }}</legend>
                    <p class="text-xs text-slate-500">{{ __('customers.addresses_hint') }}</p>
                    @php
                        $addressRows = old('addresses');
                        if ($addressRows === null) {
                            $addressRows = $customer->addresses->map(fn ($a) => [
                                'label' => $a->label,
                                'address_line' => $a->address_line,
                                'department_id' => $a->department_id,
                                'city_id' => $a->city_id,
                                'reference_notes' => $a->reference_notes,
                                'is_default' => $a->is_default,
                            ])->values()->all();
                        }
                        if (count($addressRows) === 0) {
                            $addressRows = [['label' => '', 'address_line' => '', 'department_id' => '', 'city_id' => '', 'reference_notes' => '', 'is_default' => false]];
                        }
                    @endphp
                    @foreach ($addressRows as $i => $row)
                        <div class="grid gap-4 sm:grid-cols-2 border-t border-slate-200 pt-4 first:border-0 first:pt-0">
                            <div>
                                <label class="text-xs font-semibold text-slate-700">{{ __('customers.address_label') }}</label>
                                <input type="text" name="addresses[{{ $i }}][label]" value="{{ $row['label'] ?? '' }}" class="mt-1 block w-full rounded-xl border-slate-300" />
                            </div>
                            <div class="flex items-end">
                                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                    <input type="checkbox" name="addresses[{{ $i }}][is_default]" value="1" @checked(!empty($row['is_default'])) />
                                    {{ __('customers.address_default') }}
                                </label>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="text-xs font-semibold text-slate-700">{{ __('customers.address_line') }}</label>
                                <textarea name="addresses[{{ $i }}][address_line]" rows="2" class="mt-1 block w-full rounded-xl border-slate-300">{{ $row['address_line'] ?? '' }}</textarea>
                            </div>
                            <div data-geo-cascade data-cities-url="{{ route('geo.cities') }}" data-old-dept="{{ $row['department_id'] ?? '' }}" data-old-city="{{ $row['city_id'] ?? '' }}" class="contents">
                                <div>
                                    <label class="text-xs font-semibold text-slate-700">{{ __('shipments.department') }}</label>
                                    <select name="addresses[{{ $i }}][department_id]" data-role="department" class="mt-1 block w-full rounded-xl border-slate-300">
                                        <option value="">{{ __('shipments.select_department') }}</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}" @selected(($row['department_id'] ?? '') == $department->id)>{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-slate-700">{{ __('shipments.city') }}</label>
                                    <select name="addresses[{{ $i }}][city_id]" data-role="city" class="mt-1 block w-full rounded-xl border-slate-300">
                                        <option value="">{{ __('shipments.select_city') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="text-xs font-semibold text-slate-700">{{ __('customers.reference_notes') }}</label>
                                <input type="text" name="addresses[{{ $i }}][reference_notes]" value="{{ $row['reference_notes'] ?? '' }}" class="mt-1 block w-full rounded-xl border-slate-300" />
                            </div>
                        </div>
                    @endforeach
                </fieldset>

                <div class="flex justify-end gap-4">
                    <a href="{{ route('customers.show', $customer) }}" class="text-sm font-semibold text-slate-600">{{ __('shipments.cancel') }}</a>
                    <x-primary-button>{{ __('customers.save_submit') }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        @include('partials.geo-cascade-script')
    @endpush
</x-app-layout>
