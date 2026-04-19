<x-marketing-layout :title="__('tracking.search_page_title')">
    <div class="bg-gradient-to-b from-brand-50/80 to-white py-14 md:py-20">
        <div class="max-w-lg mx-auto px-4 sm:px-6">
            <div class="text-center mb-8">
                <h1 class="text-2xl md:text-3xl font-bold text-slate-900">{{ __('tracking.search_heading') }}</h1>
                <p class="mt-2 text-sm text-slate-600">{{ __('tracking.search_intro') }}</p>
            </div>

            <div class="bg-white shadow-brand rounded-2xl border border-slate-100 overflow-hidden">
                <form method="POST" action="{{ route('tracking.lookup') }}" class="p-6 md:p-8 space-y-5">
                    @csrf

                    <div>
                        <label for="tracking_number" class="block text-sm font-medium text-slate-700">{{ __('tracking.tracking_number_label') }}</label>
                        <input
                            id="tracking_number"
                            name="tracking_number"
                            type="text"
                            value="{{ old('tracking_number') }}"
                            autocomplete="off"
                            required
                            placeholder="{{ __('tracking.tracking_number_placeholder') }}"
                            class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                        />
                        @error('tracking_number')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="organization_slug" class="block text-sm font-medium text-slate-700">{{ __('tracking.organization_slug_label') }}</label>
                        <p class="text-xs text-slate-500 mb-1">{{ __('tracking.organization_slug_help') }}</p>
                        <input
                            id="organization_slug"
                            name="organization_slug"
                            type="text"
                            value="{{ old('organization_slug') }}"
                            autocomplete="organization"
                            placeholder="{{ __('tracking.organization_slug_placeholder') }}"
                            class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                        />
                        @error('organization_slug')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="w-full flex justify-center py-3 px-4 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition"
                    >
                        {{ __('tracking.search_submit') }}
                    </button>
                </form>
            </div>

            <p class="text-center mt-8 text-sm text-slate-500">
                <a href="{{ url('/') }}" class="text-brand-600 font-medium hover:text-brand-800">{{ __('tracking.back_home') }}</a>
            </p>
        </div>
    </div>
</x-marketing-layout>
