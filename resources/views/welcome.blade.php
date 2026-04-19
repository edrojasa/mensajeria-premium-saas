<x-marketing-layout :title="__('brand.hero_title')">
    {{-- Hero --}}
    <section id="inicio" class="relative min-h-[78vh] flex items-center overflow-hidden">
        <div class="absolute inset-0 bg-slate-950" aria-hidden="true"></div>
        <div
            class="absolute inset-0 bg-cover bg-center"
            style="background-image: url('{{ asset('images/hero/logistics-bg.jpg') }}')"
            aria-hidden="true"
        ></div>
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950/95 via-slate-950/85 to-brand-950/75" aria-hidden="true"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_left,_rgba(59,130,246,0.25),transparent_50%)]" aria-hidden="true"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28 w-full">
            <div class="max-w-3xl">
                <p class="inline-flex rounded-full bg-white/10 text-white text-xs font-bold px-4 py-1.5 mb-6 ring-1 ring-white/20 backdrop-blur-sm">
                    {{ __('brand.name') }}
                </p>
                <h1 class="font-display text-4xl sm:text-5xl md:text-6xl font-extrabold text-white tracking-tight leading-[1.1]">
                    {{ __('brand.hero_title') }}
                </h1>
                <p class="mt-6 text-lg sm:text-xl text-white/85 leading-relaxed max-w-2xl">
                    {{ __('brand.hero_subtitle') }}
                </p>
                <div class="mt-10 flex flex-wrap gap-4">
                    <a href="{{ route('tracking.search') }}" class="inline-flex items-center justify-center rounded-2xl bg-white px-7 py-4 text-base font-bold text-brand-900 shadow-2xl shadow-black/30 hover:bg-brand-50 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-slate-950 transition">
                        {{ __('brand.hero_cta_tracking') }}
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-2xl border-2 border-white/40 bg-white/10 px-7 py-4 text-base font-bold text-white backdrop-blur-sm hover:bg-white/15 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:ring-offset-2 focus:ring-offset-slate-950 transition">
                        {{ __('brand.hero_cta_login') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Tracking consulta --}}
    <section class="relative py-16 md:py-24 bg-gradient-to-b from-slate-50 via-white to-slate-50 border-y border-slate-200/80">
        <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-brand-300/60 to-transparent" aria-hidden="true"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <div>
                    <h2 class="font-display text-3xl md:text-4xl font-bold text-slate-900 tracking-tight">{{ __('tracking.search_heading') }}</h2>
                    <p class="mt-4 text-lg text-slate-600">{{ __('tracking.search_intro') }}</p>
                    <ul class="mt-8 space-y-4">
                        <li class="flex gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-100 text-brand-700 font-bold text-sm">1</span>
                            <div>
                                <p class="font-semibold text-slate-900">{{ __('brand.step_track_1_title') }}</p>
                                <p class="text-sm text-slate-600">{{ __('brand.step_track_1_desc') }}</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-100 text-brand-700 font-bold text-sm">2</span>
                            <div>
                                <p class="font-semibold text-slate-900">{{ __('brand.step_track_2_title') }}</p>
                                <p class="text-sm text-slate-600">{{ __('brand.step_track_2_desc') }}</p>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="rounded-3xl bg-white p-8 sm:p-10 shadow-2xl shadow-brand-900/15 border border-slate-200/90 ring-1 ring-slate-900/5">
                    <form method="POST" action="{{ route('tracking.lookup') }}" class="space-y-5">
                        @csrf
                        <div>
                            <label for="landing_tracking" class="block text-sm font-semibold text-slate-800">{{ __('tracking.tracking_number_label') }}</label>
                            <input
                                id="landing_tracking"
                                name="tracking_number"
                                type="text"
                                value="{{ old('tracking_number') }}"
                                required
                                autocomplete="off"
                                placeholder="{{ __('brand.hero_tracking_placeholder') }}"
                                class="mt-2 block w-full rounded-2xl border-slate-200 shadow-inner bg-slate-50/80 py-3.5 px-4 text-slate-900 focus:border-brand-500 focus:ring-brand-500"
                            />
                            @error('tracking_number')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="landing_org" class="block text-sm font-semibold text-slate-800">{{ __('tracking.organization_slug_label') }}</label>
                            <p class="text-xs text-slate-500 mt-1">{{ __('tracking.organization_slug_help') }}</p>
                            <input
                                id="landing_org"
                                name="organization_slug"
                                type="text"
                                value="{{ old('organization_slug') }}"
                                placeholder="{{ __('tracking.organization_slug_placeholder') }}"
                                class="mt-2 block w-full rounded-2xl border-slate-200 shadow-inner bg-slate-50/80 py-3.5 px-4 focus:border-brand-500 focus:ring-brand-500"
                            />
                            @error('organization_slug')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="w-full flex justify-center py-4 px-4 rounded-2xl bg-brand-600 text-white text-base font-bold shadow-xl shadow-brand-600/30 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition">
                            {{ __('tracking.search_submit') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- Servicios --}}
    <section id="servicios" class="py-16 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto">
                <h2 class="font-display text-3xl md:text-4xl font-bold text-slate-900">{{ __('brand.services_title') }}</h2>
                <p class="mt-4 text-lg text-slate-600">{{ __('brand.services_intro') }}</p>
            </div>
            <div class="mt-14 grid md:grid-cols-3 gap-8 lg:gap-10">
                <article class="group rounded-3xl border border-slate-200/90 bg-gradient-to-b from-white to-slate-50/80 p-8 shadow-xl shadow-slate-900/5 hover:shadow-2xl hover:border-brand-200/80 transition duration-300">
                    <img src="{{ asset('images/services/delivery.svg') }}" alt="" class="h-14 w-14 rounded-2xl shadow-lg shadow-brand-900/10 mb-6 ring-1 ring-slate-900/5" width="56" height="56" />
                    <h3 class="font-display text-xl font-bold text-slate-900">{{ __('brand.services_delivery') }}</h3>
                    <p class="mt-3 text-slate-600 leading-relaxed">{{ __('brand.services_delivery_desc') }}</p>
                </article>
                <article class="group rounded-3xl border border-slate-200/90 bg-gradient-to-b from-white to-slate-50/80 p-8 shadow-xl shadow-slate-900/5 hover:shadow-2xl hover:border-brand-200/80 transition duration-300">
                    <img src="{{ asset('images/services/tracking.svg') }}" alt="" class="h-14 w-14 rounded-2xl shadow-lg shadow-brand-900/10 mb-6 ring-1 ring-slate-900/5" width="56" height="56" />
                    <h3 class="font-display text-xl font-bold text-slate-900">{{ __('brand.services_tracking') }}</h3>
                    <p class="mt-3 text-slate-600 leading-relaxed">{{ __('brand.services_tracking_desc') }}</p>
                </article>
                <article class="group rounded-3xl border border-slate-200/90 bg-gradient-to-b from-white to-slate-50/80 p-8 shadow-xl shadow-slate-900/5 hover:shadow-2xl hover:border-brand-200/80 transition duration-300">
                    <img src="{{ asset('images/services/coverage.svg') }}" alt="" class="h-14 w-14 rounded-2xl shadow-lg shadow-brand-900/10 mb-6 ring-1 ring-slate-900/5" width="56" height="56" />
                    <h3 class="font-display text-xl font-bold text-slate-900">{{ __('brand.services_coverage') }}</h3>
                    <p class="mt-3 text-slate-600 leading-relaxed">{{ __('brand.services_coverage_desc') }}</p>
                </article>
            </div>
        </div>
    </section>

    {{-- Contacto --}}
    <section id="contacto" class="py-16 md:py-24 bg-gradient-to-b from-slate-100 to-slate-50 border-t border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="font-display text-3xl md:text-4xl font-bold text-slate-900">{{ __('brand.contact_title') }}</h2>
                <p class="mt-4 text-lg text-slate-600">{{ __('brand.contact_intro') }}</p>
            </div>
            <div class="mt-12 max-w-xl mx-auto rounded-3xl bg-white border border-slate-200/90 p-10 shadow-2xl shadow-slate-900/10 ring-1 ring-slate-900/5 text-left space-y-6">
                <div class="flex justify-between gap-4 text-sm border-b border-slate-100 pb-5">
                    <span class="font-semibold text-slate-800">{{ __('brand.contact_phone_label') }}</span>
                    <span class="text-slate-600">{{ __('brand.contact_phone_value') }}</span>
                </div>
                <div class="flex justify-between gap-4 text-sm border-b border-slate-100 pb-5">
                    <span class="font-semibold text-slate-800">{{ __('brand.contact_email_label') }}</span>
                    <span class="text-slate-600 break-all">{{ __('brand.contact_email_value') }}</span>
                </div>
                <div class="flex justify-between gap-4 text-sm">
                    <span class="font-semibold text-slate-800">{{ __('brand.contact_hours_label') }}</span>
                    <span class="text-slate-600">{{ __('brand.contact_hours_value') }}</span>
                </div>
            </div>
        </div>
    </section>
</x-marketing-layout>
