<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-display text-2xl md:text-3xl font-bold text-slate-900">{{ __('users.title') }}</h2>
                <p class="mt-2 text-sm text-slate-600">{{ __('users.subtitle') }}</p>
            </div>
            @if ($canManage)
                <a href="{{ route('users.create') }}" class="inline-flex justify-center items-center rounded-2xl bg-brand-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-brand-600/25 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition shrink-0">
                    {{ __('users.create_button') }}
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-10 md:py-12 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <x-auth-session-status class="mb-2" :status="session('status')" />

        @if (auth()->user()->canExportTenantReports())
            <div class="flex flex-wrap gap-3 justify-end">
                <a href="{{ route('exports.users.excel', request()->query()) }}" class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-bold text-slate-800 shadow-sm hover:bg-slate-50">{{ __('exports.excel') }}</a>
                <a href="{{ route('exports.users.pdf', request()->query()) }}" class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-bold text-slate-800 shadow-sm hover:bg-slate-50">{{ __('exports.pdf') }}</a>
                <a href="{{ route('exports.messengers.excel', request()->query()) }}" class="inline-flex items-center rounded-xl bg-brand-600 px-4 py-2 text-sm font-bold text-white shadow-md hover:bg-brand-700">{{ __('exports.messengers_excel') }}</a>
                <a href="{{ route('exports.messengers.pdf', request()->query()) }}" class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-bold text-white shadow-md hover:bg-slate-800">{{ __('exports.messengers_pdf') }}</a>
            </div>
        @endif

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl ring-1 ring-slate-900/5">
            <form method="GET" action="{{ route('users.index') }}" class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-end">
                <div class="flex-1 min-w-[12rem]">
                    <x-input-label for="filter_role" :value="__('users.filter_role')" />
                    <select id="filter_role" name="role" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm text-sm">
                        <option value="">{{ __('users.filter_role_all') }}</option>
                        @foreach (\App\Organizations\OrganizationRole::ALL as $role)
                            <option value="{{ $role }}" @selected($roleFilter === $role)>{{ \App\Organizations\OrganizationRole::label($role) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <x-primary-button type="submit" class="rounded-2xl">{{ __('shipments.filter_apply') }}</x-primary-button>
                    <a href="{{ route('users.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-5 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50">{{ __('shipments.filter_clear') }}</a>
                </div>
            </form>
        </div>

        @if ($canManage)
            <div class="rounded-3xl border border-brand-100 bg-gradient-to-br from-brand-50/80 to-white p-8 shadow-xl ring-1 ring-brand-900/5">
                <p class="text-xs font-bold uppercase tracking-wider text-brand-600">{{ __('users.section_create_tag') }}</p>
                <h3 class="mt-2 font-display text-xl font-bold text-slate-900">{{ __('users.section_create_heading') }}</h3>
                <p class="mt-3 text-sm text-slate-600 leading-relaxed max-w-2xl">{{ __('users.section_create_body') }}</p>
                <div class="mt-6">
                    <a href="{{ route('users.create') }}" class="inline-flex justify-center items-center rounded-2xl bg-brand-600 px-6 py-3 text-sm font-bold text-white shadow-md hover:bg-brand-700">{{ __('users.create_button') }}</a>
                </div>
            </div>
        @endif

        <div class="rounded-3xl border border-slate-200 bg-white shadow-xl overflow-hidden ring-1 ring-slate-900/5">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gradient-to-r from-slate-100 to-slate-50">
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase text-slate-600">{{ __('users.column_name') }}</th>
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase text-slate-600">{{ __('users.column_email') }}</th>
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase text-slate-600">{{ __('users.column_phone') }}</th>
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase text-slate-600">{{ __('users.column_role') }}</th>
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase text-slate-600">{{ __('users.column_status') }}</th>
                            @if ($canManage)
                                <th class="px-4 py-4 text-right text-xs font-bold uppercase text-slate-600">{{ __('users.actions') }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($orgUsers as $user)
                            @php
                                $org = $user->organizations->first();
                                $pivot = $org?->pivot;
                                $fid = 'org-user-form-'.$user->id;
                            @endphp
                            <tr class="hover:bg-brand-50/40 align-top">
                                <td class="px-4 py-4 font-semibold text-slate-900">{{ $user->name }}</td>
                                <td class="px-4 py-4 text-slate-700">{{ $user->email }}</td>
                                @if ($canManage)
                                    <td class="px-4 py-4">
                                        <form action="{{ route('users.update', $user) }}" method="POST" id="{{ $fid }}" class="hidden">
                                            @csrf
                                            @method('PATCH')
                                        </form>
                                        <input form="{{ $fid }}" name="phone" value="{{ old('phone', $user->phone) }}" class="block w-full min-w-[9rem] rounded-xl border-slate-300 text-sm shadow-sm" />
                                    </td>
                                    <td class="px-4 py-4">
                                        <select form="{{ $fid }}" name="role" class="block w-full rounded-xl border-slate-300 text-sm shadow-sm">
                                            @foreach (\App\Organizations\OrganizationRole::ALL as $role)
                                                <option value="{{ $role }}" @selected($pivot->role === $role)>{{ \App\Organizations\OrganizationRole::label($role) }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-4">
                                        <input type="hidden" form="{{ $fid }}" name="is_active" value="0" />
                                        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                            <input type="checkbox" form="{{ $fid }}" name="is_active" value="1" class="rounded border-slate-300 text-brand-600" @checked($pivot->is_active) />
                                            {{ __('users.active') }}
                                        </label>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <button type="submit" form="{{ $fid }}" class="inline-flex items-center rounded-xl bg-brand-600 px-4 py-2 text-xs font-bold text-white shadow-md hover:bg-brand-700">{{ __('users.save') }}</button>
                                    </td>
                                @else
                                    <td class="px-4 py-4 text-slate-600">{{ $user->phone ?? '—' }}</td>
                                    <td class="px-4 py-4"><span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-800">{{ \App\Organizations\OrganizationRole::label($pivot->role) }}</span></td>
                                    <td class="px-4 py-4"><span class="text-sm font-semibold {{ $pivot->is_active ? 'text-emerald-700' : 'text-slate-500' }}">{{ $pivot->is_active ? __('users.active') : __('users.inactive') }}</span></td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
