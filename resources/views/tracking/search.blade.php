<x-marketing-layout :title="__('tracking.search_page_title')">
    <section class="relative overflow-hidden bg-slate-950 py-16 md:py-24">
        <div class="absolute inset-0 opacity-40 bg-cover bg-center" style="background-image: url('{{ asset('images/hero/logistics-bg.jpg') }}')" aria-hidden="true"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-slate-950/90 via-brand-950/85 to-slate-950" aria-hidden="true"></div>
        <div class="relative max-w-lg mx-auto px-4 sm:px-6 text-center">
            <p class="text-xs font-bold uppercase tracking-widest text-brand-300 mb-4">{{ __('brand.nav_tracking') }}</p>
            <h1 class="font-display text-3xl md:text-4xl font-extrabold text-white tracking-tight">{{ __('tracking.search_heading') }}</h1>
            <p class="mt-4 text-base text-white/80">{{ __('tracking.search_intro') }}</p>
        </div>
    </section>

    <div class="-mt-8 relative z-10 max-w-lg mx-auto px-4 sm:px-6 pb-20">
        <div class="rounded-3xl bg-white p-8 md:p-10 shadow-2xl shadow-slate-900/20 border border-slate-100 ring-1 ring-slate-900/5">
            <form method="POST" action="{{ route('tracking.lookup') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="tracking_number" class="block text-sm font-bold text-slate-800">{{ __('tracking.tracking_number_label') }}</label>
                    <input
                        id="tracking_number"
                        name="tracking_number"
                        type="text"
                        value="{{ old('tracking_number') }}"
                        autocomplete="off"
                        required
                        placeholder="{{ __('tracking.tracking_number_placeholder') }}"
                        class="mt-2 block w-full rounded-2xl border-slate-200 bg-slate-50/80 py-3.5 px-4 shadow-inner focus:border-brand-500 focus:ring-brand-500"
                    />
                    @error('tracking_number')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="organization_slug" class="block text-sm font-bold text-slate-800">{{ __('tracking.organization_slug_label') }}</label>
                    <p class="text-xs text-slate-500 mt-1">{{ __('tracking.organization_slug_help') }}</p>
                    <input
                        id="organization_slug"
                        name="organization_slug"
                        type="text"
                        value="{{ old('organization_slug') }}"
                        autocomplete="organization"
                        placeholder="{{ __('tracking.organization_slug_placeholder') }}"
                        class="mt-2 block w-full rounded-2xl border-slate-200 bg-slate-50/80 py-3.5 px-4 shadow-inner focus:border-brand-500 focus:ring-brand-500"
                    />
                    @error('organization_slug')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="w-full flex justify-center py-4 px-4 rounded-2xl bg-brand-600 text-white text-base font-bold shadow-xl shadow-brand-600/30 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition"
                >
                    {{ __('tracking.search_submit') }}
                </button>
            </form>
        </div>

        <p class="text-center mt-10 text-sm text-slate-500">
            <a href="{{ url('/') }}" class="font-semibold text-brand-600 hover:text-brand-800">{{ __('tracking.back_home') }}</a>
        </p>
    </div>
</x-marketing-layout>
