<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-display text-2xl md:text-3xl font-bold text-slate-900">{{ __('logs.title') }}</h2>
            <p class="mt-2 text-sm text-slate-600">{{ __('logs.subtitle') }}</p>
        </div>
    </x-slot>

    <div class="py-10 md:py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl ring-1 ring-slate-900/5">
            <form method="GET" action="{{ route('logs.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-5 lg:items-end">
                <div>
                    <x-input-label for="log_user" :value="__('logs.filter_user')" />
                    <select id="log_user" name="user_id" class="mt-2 block w-full rounded-xl border-slate-300 shadow-sm text-sm">
                        <option value="">{{ __('logs.filter_all_users') }}</option>
                        @foreach ($usersForFilter as $u)
                            <option value="{{ $u->id }}" @selected((string) request('user_id') === (string) $u->id)>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="log_action" :value="__('logs.filter_action')" />
                    <select id="log_action" name="action" class="mt-2 block w-full rounded-xl border-slate-300 shadow-sm text-sm">
                        <option value="">{{ __('logs.filter_all_actions') }}</option>
                        @foreach ($actions as $action)
                            <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="log_from" :value="__('logs.filter_date_from')" />
                    <x-text-input id="log_from" name="date_from" type="date" class="mt-2 block w-full rounded-xl" :value="request('date_from')" />
                </div>
                <div>
                    <x-input-label for="log_to" :value="__('logs.filter_date_to')" />
                    <x-text-input id="log_to" name="date_to" type="date" class="mt-2 block w-full rounded-xl" :value="request('date_to')" />
                </div>
                <div class="flex gap-2 lg:col-span-1">
                    <x-primary-button type="submit" class="rounded-xl w-full justify-center">{{ __('logs.filter_submit') }}</x-primary-button>
                </div>
            </form>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white shadow-xl overflow-hidden ring-1 ring-slate-900/5">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gradient-to-r from-slate-100 to-slate-50">
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase text-slate-600">{{ __('logs.column_date') }}</th>
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase text-slate-600">{{ __('logs.column_user') }}</th>
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase text-slate-600">{{ __('logs.column_action') }}</th>
                            <th class="px-4 py-4 text-left text-xs font-bold uppercase text-slate-600">{{ __('logs.column_description') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($logs as $log)
                            <tr class="hover:bg-brand-50/40">
                                <td class="px-4 py-3 whitespace-nowrap text-slate-600">{{ $log->created_at->timezone(config('app.timezone'))->format('d/m/Y H:i:s') }}</td>
                                <td class="px-4 py-3 text-slate-900 font-medium">{{ $log->user?->name ?? '—' }}</td>
                                <td class="px-4 py-3 font-mono text-xs text-brand-800">{{ $log->action }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $log->description ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-12 text-center text-slate-600">{{ __('logs.empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($logs->hasPages())
                <div class="px-4 py-4 border-t border-slate-100">{{ $logs->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
