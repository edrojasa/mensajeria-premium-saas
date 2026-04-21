<x-marketing-layout :title="__('brand.hero_title')">
    {{-- Hero --}}
    <section id="inicio" class="relative min-h-[80vh] flex items-center overflow-hidden">
        <div class="absolute inset-0 bg-slate-950" aria-hidden="true"></div>
        <div
            class="absolute inset-0 bg-cover bg-center brightness-[0.45]"
            style="background-image: url('{{ asset('images/hero/logistics-bg.jpg') }}')"
            aria-hidden="true"
        ></div>
        <div class="absolute inset-0 bg-gradient-to-br from-brand-950/92 via-slate-950/88 to-brand-900/90" aria-hidden="true"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-brand-600/35 via-transparent to-indigo-900/40" aria-hidden="true"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_120%_80%_at_70%_-20%,rgba(59,130,246,0.35),transparent_55%)]" aria-hidden="true"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-transparent" aria-hidden="true"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32 w-full">
            <div class="max-w-3xl">
                <p class="inline-flex rounded-full bg-white/15 text-white text-xs font-bold px-4 py-2 mb-7 ring-1 ring-white/25 backdrop-blur-md shadow-lg shadow-black/20">
                    {{ __('brand.name') }}
                </p>
                <h1 class="font-display text-4xl sm:text-5xl md:text-6xl font-extrabold text-white tracking-tight leading-[1.08] drop-shadow-[0_4px_24px_rgba(0,0,0,0.35)]">
                    {{ __('brand.hero_title') }}
                </h1>
                <p class="mt-7 text-lg sm:text-xl text-white/90 leading-relaxed max-w-2xl font-medium">
                    {{ __('brand.hero_subtitle') }}
                </p>
                <div class="mt-11 flex flex-wrap gap-4">
                    <a href="{{ route('tracking.search') }}" class="landing-cta-primary inline-flex items-center justify-center rounded-2xl px-8 py-4 text-base font-bold text-brand-900 bg-gradient-to-r from-white via-white to-brand-50 shadow-2xl shadow-brand-900/35 hover:shadow-[0_20px_40px_-8px_rgba(37,99,235,0.55)] hover:scale-[1.03] active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-slate-950 transition duration-300 ease-out">
                        {{ __('brand.hero_cta_tracking') }}
                    </a>
                    <a href="{{ route('login') }}" class="landing-cta-secondary inline-flex items-center justify-center rounded-2xl border-2 border-white/45 bg-white/10 px-8 py-4 text-base font-bold text-white backdrop-blur-md hover:bg-white/18 hover:border-white/60 hover:scale-[1.03] hover:shadow-xl hover:shadow-black/25 active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-brand-400 focus:ring-offset-2 focus:ring-offset-slate-950 transition duration-300 ease-out">
                        {{ __('brand.hero_cta_login') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Tracking consulta --}}
    <section class="relative py-16 md:py-24 bg-gradient-to-b from-slate-50 via-white to-slate-50 border-y border-slate-200/80">
        <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-brand-400/50 to-transparent" aria-hidden="true"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <div>
                    <p class="text-sm font-bold uppercase tracking-wider text-brand-600">{{ __('brand.landing_tracking_heading') }}</p>
                    <h2 class="mt-3 font-display text-3xl md:text-4xl font-bold text-slate-900 tracking-tight">{{ __('brand.landing_tracking_tagline') }}</h2>
                    <p class="mt-4 text-lg text-slate-600 leading-relaxed">{{ __('brand.landing_tracking_intro') }}</p>
                    <ul class="mt-10 space-y-5">
                        <li class="flex gap-4">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white font-bold text-sm shadow-lg shadow-brand-600/30">1</span>
                            <div>
                                <p class="font-semibold text-slate-900">{{ __('brand.step_track_1_title') }}</p>
                                <p class="text-sm text-slate-600">{{ __('brand.step_track_1_desc') }}</p>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white font-bold text-sm shadow-lg shadow-brand-600/30">2</span>
                            <div>
                                <p class="font-semibold text-slate-900">{{ __('brand.step_track_2_title') }}</p>
                                <p class="text-sm text-slate-600">{{ __('brand.step_track_2_desc') }}</p>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="group relative rounded-3xl bg-white p-8 sm:p-10 shadow-2xl shadow-brand-900/20 border border-brand-200/70 ring-2 ring-brand-500/15 hover:shadow-[0_28px_56px_-12px_rgba(37,99,235,0.28)] hover:-translate-y-1 hover:border-brand-300/90 transition duration-300 ease-out">
                    <div class="flex items-center gap-4 mb-8 pb-6 border-b border-slate-100">
                        <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-lg shadow-brand-600/35 ring-4 ring-brand-500/15" aria-hidden="true">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        </span>
                        <div>
                            <p class="text-sm font-bold text-brand-700">{{ __('tracking.search_page_title') }}</p>
                            <p class="text-xs text-slate-500">{{ __('brand.landing_tracking_tagline') }}</p>
                        </div>
                    </div>
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
                                class="mt-2 block w-full rounded-2xl border-slate-200 shadow-inner bg-slate-50/90 py-3.5 px-4 text-slate-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30"
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
                                class="mt-2 block w-full rounded-2xl border-slate-200 shadow-inner bg-slate-50/90 py-3.5 px-4 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30"
                            />
                            @error('organization_slug')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="w-full flex justify-center py-4 px-4 rounded-2xl bg-gradient-to-r from-brand-600 to-brand-700 text-white text-base font-bold shadow-xl shadow-brand-600/35 hover:shadow-2xl hover:shadow-brand-600/45 hover:scale-[1.02] active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition duration-300">
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
            <div class="mt-14 grid sm:grid-cols-2 xl:grid-cols-4 gap-8 lg:gap-8">
                <article class="landing-service-card group rounded-3xl border border-slate-200/90 bg-gradient-to-b from-white to-slate-50/90 p-8 shadow-xl shadow-slate-900/[0.06] ring-1 ring-slate-900/5 hover:-translate-y-2 hover:shadow-2xl hover:shadow-brand-900/15 hover:border-brand-300/80 hover:ring-brand-500/10 transition duration-300 ease-out">
                    <img src="{{ asset('images/services/delivery.svg') }}" alt="" class="h-16 w-16 rounded-2xl shadow-lg shadow-brand-900/15 mb-6 ring-1 ring-slate-900/5 transition duration-300 group-hover:scale-105" width="64" height="64" />
                    <h3 class="font-display text-xl font-bold text-slate-900">{{ __('brand.services_delivery') }}</h3>
                    <p class="mt-3 text-slate-600 leading-relaxed text-[0.9375rem]">{{ __('brand.services_delivery_desc') }}</p>
                </article>
                <article class="landing-service-card group rounded-3xl border border-slate-200/90 bg-gradient-to-b from-white to-slate-50/90 p-8 shadow-xl shadow-slate-900/[0.06] ring-1 ring-slate-900/5 hover:-translate-y-2 hover:shadow-2xl hover:shadow-brand-900/15 hover:border-brand-300/80 hover:ring-brand-500/10 transition duration-300 ease-out">
                    <img src="{{ asset('images/services/tracking.svg') }}" alt="" class="h-16 w-16 rounded-2xl shadow-lg shadow-brand-900/15 mb-6 ring-1 ring-slate-900/5 transition duration-300 group-hover:scale-105" width="64" height="64" />
                    <h3 class="font-display text-xl font-bold text-slate-900">{{ __('brand.services_tracking') }}</h3>
                    <p class="mt-3 text-slate-600 leading-relaxed text-[0.9375rem]">{{ __('brand.services_tracking_desc') }}</p>
                </article>
                <article class="landing-service-card group rounded-3xl border border-slate-200/90 bg-gradient-to-b from-white to-slate-50/90 p-8 shadow-xl shadow-slate-900/[0.06] ring-1 ring-slate-900/5 hover:-translate-y-2 hover:shadow-2xl hover:shadow-brand-900/15 hover:border-brand-300/80 hover:ring-brand-500/10 transition duration-300 ease-out">
                    <img src="{{ asset('images/services/coverage.svg') }}" alt="" class="h-16 w-16 rounded-2xl shadow-lg shadow-brand-900/15 mb-6 ring-1 ring-slate-900/5 transition duration-300 group-hover:scale-105" width="64" height="64" />
                    <h3 class="font-display text-xl font-bold text-slate-900">{{ __('brand.services_coverage') }}</h3>
                    <p class="mt-3 text-slate-600 leading-relaxed text-[0.9375rem]">{{ __('brand.services_coverage_desc') }}</p>
                </article>
                <article class="landing-service-card group rounded-3xl border border-slate-200/90 bg-gradient-to-b from-white to-slate-50/90 p-8 shadow-xl shadow-slate-900/[0.06] ring-1 ring-slate-900/5 hover:-translate-y-2 hover:shadow-2xl hover:shadow-brand-900/15 hover:border-brand-300/80 hover:ring-brand-500/10 transition duration-300 ease-out">
                    <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 to-brand-700 text-white shadow-lg shadow-brand-900/25 ring-1 ring-slate-900/5 transition duration-300 group-hover:scale-105" aria-hidden="true">
                        <svg class="h-9 w-9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-display text-xl font-bold text-slate-900">{{ __('brand.services_finance') }}</h3>
                    <p class="mt-3 text-slate-600 leading-relaxed text-[0.9375rem]">{{ __('brand.services_finance_desc') }}</p>
                </article>
            </div>
        </div>
    </section>

    {{-- Diferencial --}}
    <section class="relative py-16 md:py-24 bg-gradient-to-b from-slate-50 to-white border-y border-slate-200/80 overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,rgba(59,130,246,0.08),transparent_50%)] pointer-events-none" aria-hidden="true"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <h2 class="font-display text-3xl md:text-4xl font-bold text-slate-900">{{ __('brand.why_title') }}</h2>
                <p class="mt-4 text-lg text-slate-600">{{ __('brand.why_intro') }}</p>
            </div>
            <div class="grid md:grid-cols-2 gap-x-12 gap-y-6 max-w-5xl mx-auto">
                @foreach ([
                    __('brand.why_multi_tenant'),
                    __('brand.why_couriers'),
                    __('brand.why_status'),
                    __('brand.why_reports'),
                    __('brand.why_finance'),
                ] as $point)
                    <div class="flex gap-4 p-5 rounded-2xl border border-slate-200/80 bg-white/80 shadow-md shadow-slate-900/5 hover:shadow-lg hover:border-brand-200/90 hover:-translate-y-0.5 transition duration-300">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-100 text-brand-700 ring-1 ring-brand-200/80">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <p class="text-slate-800 font-medium leading-snug pt-1.5">{{ $point }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Contacto --}}
    <section id="contacto" class="py-16 md:py-24 bg-gradient-to-b from-white via-slate-50 to-slate-100 border-t border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="font-display text-3xl md:text-4xl font-bold text-slate-900">{{ __('brand.contact_title') }}</h2>
                <p class="mt-4 text-lg text-slate-600">{{ __('brand.contact_intro') }}</p>
            </div>
            <div class="mt-12 max-w-lg mx-auto rounded-3xl bg-white border border-slate-200/90 p-8 sm:p-10 shadow-2xl shadow-slate-900/15 ring-1 ring-slate-900/5 space-y-8">
                <div class="flex gap-5 items-start">
                    <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-lg shadow-brand-600/25">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </span>
                    <div class="min-w-0 pt-1">
                        <p class="text-xs font-bold uppercase tracking-wide text-brand-700">{{ __('brand.contact_phone_label') }}</p>
                        <p class="mt-1 text-lg font-semibold text-slate-900">{{ __('brand.contact_phone_value') }}</p>
                    </div>
                </div>
                <div class="flex gap-5 items-start pt-2 border-t border-slate-100">
                    <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-lg shadow-brand-600/25">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </span>
                    <div class="min-w-0 pt-1">
                        <p class="text-xs font-bold uppercase tracking-wide text-brand-700">{{ __('brand.contact_email_label') }}</p>
                        <p class="mt-1 text-lg font-semibold text-slate-900 break-all">{{ __('brand.contact_email_value') }}</p>
                    </div>
                </div>
                <div class="flex gap-5 items-start pt-2 border-t border-slate-100">
                    <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-lg shadow-brand-600/25">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                    <div class="min-w-0 pt-1">
                        <p class="text-xs font-bold uppercase tracking-wide text-brand-700">{{ __('brand.contact_hours_label') }}</p>
                        <p class="mt-1 text-lg font-semibold text-slate-900">{{ __('brand.contact_hours_value') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-marketing-layout>
