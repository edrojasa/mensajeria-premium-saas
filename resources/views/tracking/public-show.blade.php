<x-marketing-layout :title="__('tracking.public_title')">
    <div class="bg-gradient-to-b from-brand-50/80 to-slate-50 py-10 md:py-14">
        <div class="max-w-xl mx-auto px-4">
            <div class="rounded-2xl bg-white shadow-brand border border-slate-100 overflow-hidden">
                <div class="bg-gradient-to-r from-brand-700 to-brand-600 px-6 py-5">
                    <p class="text-brand-100 text-sm">{{ $organization->name }}</p>
                    <h1 class="text-white text-lg font-semibold mt-1">{{ __('tracking.public_heading') }}</h1>
                    <p class="text-white font-mono mt-2 tracking-wide text-lg">{{ $shipment->tracking_number }}</p>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('tracking.current_status') }}</p>
                        <p class="text-xl font-semibold text-slate-900 mt-1">{{ $shipment->statusLabel() }}</p>
                    </div>
                    <div class="border-t border-slate-100 pt-5">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">{{ __('tracking.timeline') }}</p>
                        <ul class="space-y-3">
                            @foreach ($timeline as $entry)
                                <li class="flex justify-between gap-4 text-sm">
                                    <span class="text-slate-700">{{ $entry['label'] }}</span>
                                    <span class="text-slate-500 whitespace-nowrap">{{ $entry['at']->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <p class="text-xs text-slate-400 pt-2 border-t border-slate-100">{{ __('tracking.public_disclaimer') }}</p>
                </div>
            </div>
            <p class="text-center mt-8 text-sm text-slate-500">
                <a href="{{ url('/') }}" class="text-brand-600 font-medium hover:text-brand-800">{{ __('tracking.back_home') }}</a>
            </p>
        </div>
    </div>
</x-marketing-layout>
