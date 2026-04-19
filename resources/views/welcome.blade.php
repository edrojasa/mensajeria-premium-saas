<x-marketing-layout :title="__('brand.hero_title')">
    <section id="inicio" class="relative overflow-hidden bg-gradient-to-br from-brand-50 via-white to-sky-50">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-brand-100/40 via-transparent to-transparent pointer-events-none" aria-hidden="true"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 lg:py-28">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <div>
                    <p class="inline-flex rounded-full bg-brand-100 text-brand-800 text-xs font-semibold px-3 py-1 mb-4">{{ __('brand.name') }}</p>
                    <h1 class="text-4xl sm:text-5xl font-bold text-slate-900 tracking-tight leading-tight">{{ __('brand.hero_title') }}</h1>
                    <p class="mt-5 text-lg text-slate-600 max-w-xl">{{ __('brand.hero_subtitle') }}</p>
                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="{{ route('tracking.search') }}" class="inline-flex items-center rounded-xl bg-brand-600 px-5 py-3 text-sm font-semibold text-white shadow-brand hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition">
                            {{ __('brand.nav_tracking') }}
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-800 hover:border-brand-300 hover:bg-brand-50/50 transition">
                            {{ __('brand.nav_login') }}
                        </a>
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-6 sm:p-8 shadow-brand border border-slate-100">
                    <h2 class="text-lg font-semibold text-slate-900">{{ __('tracking.search_heading') }}</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ __('tracking.search_intro') }}</p>
                    <form method="POST" action="{{ route('tracking.lookup') }}" class="mt-6 space-y-4">
                        @csrf
                        <div>
                            <label for="landing_tracking" class="block text-sm font-medium text-slate-700">{{ __('tracking.tracking_number_label') }}</label>
                            <input
                                id="landing_tracking"
                                name="tracking_number"
                                type="text"
                                value="{{ old('tracking_number') }}"
                                required
                                autocomplete="off"
                                placeholder="{{ __('brand.hero_tracking_placeholder') }}"
                                class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                            />
                            @error('tracking_number')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="landing_org" class="block text-sm font-medium text-slate-700">{{ __('tracking.organization_slug_label') }}</label>
                            <p class="text-xs text-slate-500">{{ __('tracking.organization_slug_help') }}</p>
                            <input
                                id="landing_org"
                                name="organization_slug"
                                type="text"
                                value="{{ old('organization_slug') }}"
                                placeholder="{{ __('tracking.organization_slug_placeholder') }}"
                                class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                            />
                            @error('organization_slug')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="w-full flex justify-center py-3 px-4 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition">
                            {{ __('tracking.search_submit') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section id="servicios" class="py-16 md:py-20 bg-white border-y border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto">
                <h2 class="text-2xl md:text-3xl font-bold text-slate-900">{{ __('brand.services_title') }}</h2>
                <p class="mt-3 text-slate-600">{{ __('brand.services_intro') }}</p>
            </div>
            <div class="mt-12 grid md:grid-cols-3 gap-8">
                <div class="rounded-2xl border border-slate-200 p-6 bg-slate-50/50 hover:border-brand-200 hover:shadow-md transition">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-100 text-brand-700 mb-4">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <h3 class="font-semibold text-slate-900">{{ __('brand.services_delivery') }}</h3>
                    <p class="mt-2 text-sm text-slate-600">{{ __('brand.services_delivery_desc') }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-6 bg-slate-50/50 hover:border-brand-200 hover:shadow-md transition">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-100 text-brand-700 mb-4">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </div>
                    <h3 class="font-semibold text-slate-900">{{ __('brand.services_tracking') }}</h3>
                    <p class="mt-2 text-sm text-slate-600">{{ __('brand.services_tracking_desc') }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 p-6 bg-slate-50/50 hover:border-brand-200 hover:shadow-md transition">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-100 text-brand-700 mb-4">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    </div>
                    <h3 class="font-semibold text-slate-900">{{ __('brand.services_coverage') }}</h3>
                    <p class="mt-2 text-sm text-slate-600">{{ __('brand.services_coverage_desc') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section id="contacto" class="py-16 md:py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-2xl md:text-3xl font-bold text-slate-900">{{ __('brand.contact_title') }}</h2>
                <p class="mt-3 text-slate-600">{{ __('brand.contact_intro') }}</p>
            </div>
            <div class="mt-10 max-w-xl mx-auto rounded-2xl bg-white border border-slate-200 p-8 shadow-sm text-left space-y-4">
                <div class="flex justify-between gap-4 text-sm">
                    <span class="font-medium text-slate-700">{{ __('brand.contact_phone_label') }}</span>
                    <span class="text-slate-600">{{ __('brand.contact_phone_value') }}</span>
                </div>
                <div class="flex justify-between gap-4 text-sm border-t border-slate-100 pt-4">
                    <span class="font-medium text-slate-700">{{ __('brand.contact_email_label') }}</span>
                    <span class="text-slate-600 break-all">{{ __('brand.contact_email_value') }}</span>
                </div>
                <div class="flex justify-between gap-4 text-sm border-t border-slate-100 pt-4">
                    <span class="font-medium text-slate-700">{{ __('brand.contact_hours_label') }}</span>
                    <span class="text-slate-600">{{ __('brand.contact_hours_value') }}</span>
                </div>
            </div>
        </div>
    </section>
</x-marketing-layout>
