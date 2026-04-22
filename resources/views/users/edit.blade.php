<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-display text-2xl md:text-3xl font-bold text-slate-900">{{ __('users.edit_title') }}</h2>
            <p class="mt-2 text-sm text-slate-600">{{ __('users.edit_subtitle') }}</p>
        </div>
    </x-slot>

    <div
        class="py-10 md:py-12 max-w-xl mx-auto px-4 sm:px-6 lg:px-8"
        x-data="{ showSuspend: false, showActivate: false }"
    >
        <x-auth-session-status class="mb-6" :status="session('status')" />

        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-xl ring-1 ring-slate-900/5">
            <form method="POST" action="{{ route('users.update', $member) }}" class="space-y-6">
                @csrf
                @method('PATCH')

                <div>
                    <x-input-label for="name" :value="__('users.column_name')" />
                    <x-text-input id="name" name="name" type="text" class="mt-2 block w-full rounded-2xl" :value="old('name', $member->name)" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('users.column_email')" />
                    <x-text-input id="email" name="email" type="email" class="mt-2 block w-full rounded-2xl" :value="old('email', $member->email)" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="phone" :value="__('users.create_phone_optional')" />
                    <x-text-input id="phone" name="phone" type="text" class="mt-2 block w-full rounded-2xl" :value="old('phone', $member->phone)" autocomplete="tel" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>

                <div class="rounded-2xl border border-slate-100 bg-slate-50/80 p-5 space-y-4">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">{{ __('users.edit_password_section') }}</p>
                    <div>
                        <x-input-label for="password" :value="__('users.edit_password_new')" />
                        <x-text-input id="password" name="password" type="password" class="mt-2 block w-full rounded-2xl" autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="password_confirmation" :value="__('users.edit_password_confirm')" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-2 block w-full rounded-2xl" autocomplete="new-password" />
                    </div>
                </div>

                <div>
                    <x-input-label for="role" :value="__('users.column_role')" />
                    <select id="role" name="role" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm" required>
                        @foreach (\App\Organizations\OrganizationRole::ALL as $role)
                            <option value="{{ $role }}" @selected(old('role', $pivot->role) === $role)>{{ \App\Organizations\OrganizationRole::label($role) }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>

                <div>
                    <input type="hidden" name="is_active" value="0" />
                    <label class="inline-flex items-center gap-3 text-sm text-slate-800">
                        <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-brand-600 shadow-sm" @checked(old('is_active', (bool) $pivot->is_active)) />
                        <span>{{ __('users.pivot_active_in_org') }}</span>
                    </label>
                    <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-between gap-3 pt-2">
                    <a href="{{ route('users.index') }}" class="inline-flex justify-center items-center rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-800 shadow-sm hover:bg-slate-50">{{ __('users.edit_back_list') }}</a>
                    <x-primary-button class="justify-center rounded-2xl px-8 py-3">{{ __('users.edit_save') }}</x-primary-button>
                </div>
            </form>
        </div>

        <div class="mt-10 rounded-3xl border border-slate-200 bg-white p-8 shadow-xl ring-1 ring-slate-900/5">
            <div class="flex flex-wrap items-center gap-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">{{ __('users.account_status_label') }}</span>
                @if (\App\Enums\UserAccountStatus::isSuspended($member->status))
                    <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-900 ring-1 ring-amber-200">{{ \App\Enums\UserAccountStatus::label($member->status) }}</span>
                @else
                    <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-900 ring-1 ring-emerald-200">{{ \App\Enums\UserAccountStatus::label($member->status) }}</span>
                @endif
            </div>
        </div>

        @if ($canSuspendOrActivate ?? false)
            <div class="mt-6 space-y-6">
                @if ($member->id !== auth()->id())
                    @if (\App\Enums\UserAccountStatus::isSuspended($member->status))
                        <button
                            type="button"
                            @click="showActivate = true"
                            class="inline-flex justify-center items-center rounded-2xl bg-emerald-600 px-6 py-3 text-sm font-bold text-white shadow-md hover:bg-emerald-700"
                        >
                            {{ __('users.action_activate_account') }}
                        </button>
                    @else
                        <button
                            type="button"
                            @click="showSuspend = true"
                            class="inline-flex justify-center items-center rounded-2xl border border-amber-300 bg-amber-50 px-6 py-3 text-sm font-bold text-amber-900 hover:bg-amber-100"
                        >
                            {{ __('users.action_suspend_account') }}
                        </button>
                    @endif
                @endif
            </div>

            {{-- Modal suspender --}}
            <div
                x-show="showSuspend"
                x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50"
                @keydown.escape.window="showSuspend = false"
            >
                <div class="max-w-md w-full rounded-3xl bg-white p-8 shadow-2xl" @click.away="showSuspend = false">
                    <h3 class="font-display text-lg font-bold text-slate-900">{{ __('users.confirm_suspend_title') }}</h3>
                    <p class="mt-3 text-sm text-slate-600">{{ __('users.confirm_suspend_body') }}</p>
                    <div class="mt-8 flex flex-col-reverse sm:flex-row justify-end gap-3">
                        <button type="button" @click="showSuspend = false" class="rounded-2xl border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">{{ __('users.confirm_cancel') }}</button>
                        <form method="POST" action="{{ route('users.suspend', $member) }}">
                            @csrf
                            @method('PATCH')
                            <x-primary-button type="submit" class="rounded-2xl">{{ __('users.confirm_suspend_submit') }}</x-primary-button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Modal activar --}}
            <div
                x-show="showActivate"
                x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50"
                @keydown.escape.window="showActivate = false"
            >
                <div class="max-w-md w-full rounded-3xl bg-white p-8 shadow-2xl" @click.away="showActivate = false">
                    <h3 class="font-display text-lg font-bold text-slate-900">{{ __('users.confirm_activate_title') }}</h3>
                    <p class="mt-3 text-sm text-slate-600">{{ __('users.confirm_activate_body') }}</p>
                    <div class="mt-8 flex flex-col-reverse sm:flex-row justify-end gap-3">
                        <button type="button" @click="showActivate = false" class="rounded-2xl border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">{{ __('users.confirm_cancel') }}</button>
                        <form method="POST" action="{{ route('users.activate', $member) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="inline-flex items-center rounded-2xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white shadow hover:bg-emerald-700">{{ __('users.confirm_activate_submit') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
