<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-display text-2xl md:text-3xl font-bold text-slate-900">{{ __('users.create_title') }}</h2>
            <p class="mt-2 text-sm text-slate-600">{{ __('users.create_subtitle') }}</p>
        </div>
    </x-slot>

    <div class="py-10 md:py-12 max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-auth-session-status class="mb-6" :status="session('status')" />

        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-xl ring-1 ring-slate-900/5">
            <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
                @csrf

                <div>
                    <x-input-label for="name" :value="__('users.column_name')" />
                    <x-text-input id="name" name="name" type="text" class="mt-2 block w-full rounded-2xl" :value="old('name')" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('users.column_email')" />
                    <x-text-input id="email" name="email" type="email" class="mt-2 block w-full rounded-2xl" :value="old('email')" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="phone" :value="__('users.create_phone_optional')" />
                    <x-text-input id="phone" name="phone" type="text" class="mt-2 block w-full rounded-2xl" :value="old('phone')" autocomplete="tel" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" name="password" type="password" class="mt-2 block w-full rounded-2xl" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-2 block w-full rounded-2xl" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="role" :value="__('users.column_role')" />
                    <select id="role" name="role" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm" required>
                        @foreach (\App\Organizations\OrganizationRole::ALL as $role)
                            <option value="{{ $role }}" @selected(old('role', \App\Organizations\OrganizationRole::ADMIN) === $role)>{{ \App\Organizations\OrganizationRole::label($role) }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>

                <div>
                    <input type="hidden" name="is_active" value="0" />
                    <label class="inline-flex items-center gap-3 text-sm text-slate-800">
                        <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-brand-600 shadow-sm" @checked(old('is_active', true)) />
                        <span>{{ __('users.create_active_account') }}</span>
                    </label>
                    <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-2">
                    <a href="{{ route('users.index') }}" class="inline-flex justify-center items-center rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-800 shadow-sm hover:bg-slate-50">{{ __('users.create_cancel') }}</a>
                    <x-primary-button class="justify-center rounded-2xl px-8 py-3">{{ __('users.create_submit') }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
