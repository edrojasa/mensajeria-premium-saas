@php
    $logoHref = Auth::check() ? route('dashboard') : url('/');
@endphp

<header x-data="{ mobileOpen: false }" class="sticky top-0 z-50 border-b border-white/10 shadow-xl shadow-black/25">
    <div class="absolute inset-0 bg-slate-950" aria-hidden="true"></div>
    <div
        class="absolute inset-0 bg-cover bg-center opacity-35 mix-blend-soft-light"
        style="background-image: url('{{ asset('images/hero/logistics-bg.jpg') }}')"
        aria-hidden="true"
    ></div>
    <div class="absolute inset-0 bg-gradient-to-r from-brand-950/95 via-slate-950/92 to-brand-950/95" aria-hidden="true"></div>
    <div class="absolute inset-0 bg-[linear-gradient(to_bottom,rgba(255,255,255,0.06),transparent_40%)] pointer-events-none" aria-hidden="true"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 md:h-[4.25rem] items-center justify-between gap-4">
            <div class="flex items-center gap-5 lg:gap-10 min-w-0">
                <a href="{{ $logoHref }}" class="shrink-0 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-400 focus:ring-offset-2 focus:ring-offset-slate-950">
                    <x-brand.logo variant="light" />
                </a>

                <nav class="hidden md:flex items-center gap-0.5 lg:gap-1 text-sm font-semibold">
                    <a href="{{ url('/') }}#inicio" class="px-3 py-2 rounded-lg text-white/85 hover:text-white hover:bg-white/10 transition">{{ __('brand.nav_home') }}</a>
                    <a href="{{ route('tracking.search') }}" class="px-3 py-2 rounded-lg transition @if(request()->routeIs('tracking.search')) text-white bg-white/15 shadow-inner @else text-white/85 hover:text-white hover:bg-white/10 @endif">{{ __('brand.nav_tracking') }}</a>
                    <a href="{{ url('/') }}#servicios" class="px-3 py-2 rounded-lg text-white/85 hover:text-white hover:bg-white/10 transition">{{ __('brand.nav_services') }}</a>
                    <a href="{{ url('/') }}#contacto" class="px-3 py-2 rounded-lg text-white/85 hover:text-white hover:bg-white/10 transition">{{ __('brand.nav_contact') }}</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg transition @if(request()->routeIs('dashboard')) text-white bg-white/15 shadow-inner @else text-white/85 hover:text-white hover:bg-white/10 @endif">{{ __('Dashboard') }}</a>
                        <a href="{{ route('shipments.index') }}" class="px-3 py-2 rounded-lg transition @if(request()->routeIs('shipments.*')) text-white bg-white/15 shadow-inner @else text-white/85 hover:text-white hover:bg-white/10 @endif">{{ __('shipments.menu') }}</a>
                    @endauth
                </nav>
            </div>

            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                @auth
                    @isset($navOrganizations)
                        @if ($navOrganizations->count() > 1)
                            <form method="POST" action="{{ route('organization.switch') }}" class="hidden xl:flex items-center">
                                @csrf
                                <label class="sr-only" for="nav_organization_id">{{ __('Organización activa') }}</label>
                                <select id="nav_organization_id" name="organization_id" onchange="this.form.submit()" class="text-sm max-w-[11rem] border-white/20 bg-white/10 text-white placeholder-white/60 focus:border-brand-400 focus:ring-brand-400 rounded-lg shadow-inner">
                                    @foreach ($navOrganizations as $org)
                                        <option value="{{ $org->id }}" class="text-slate-900" @selected(isset($currentOrganization) && $currentOrganization->id === $org->id)>
                                            {{ $org->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        @elseif (isset($currentOrganization) && $currentOrganization)
                            <span class="hidden xl:inline text-xs text-white/60 truncate max-w-[10rem]" title="{{ $currentOrganization->name }}">{{ $currentOrganization->name }}</span>
                        @endif
                    @endisset

                    <div class="hidden sm:flex sm:items-center">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button type="button" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-white/15 bg-white/10 text-sm font-semibold text-white hover:bg-white/15 shadow-sm transition">
                                    <span class="max-w-[8rem] truncate">{{ Auth::user()->name }}</span>
                                    <svg class="h-4 w-4 text-white/70" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-5 py-2.5 text-sm font-bold text-brand-800 shadow-lg shadow-black/25 hover:bg-brand-50 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:ring-offset-2 focus:ring-offset-slate-950 transition">
                        {{ __('brand.nav_login') }}
                    </a>
                @endauth

                <button type="button" @click="mobileOpen = ! mobileOpen" class="md:hidden inline-flex items-center justify-center rounded-xl border border-white/15 bg-white/10 p-2 text-white hover:bg-white/15" aria-expanded="false">
                    <span class="sr-only">Menú</span>
                    <svg x-show="!mobileOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="mobileOpen" x-cloak class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <div x-show="mobileOpen" x-cloak x-transition class="md:hidden border-t border-white/10 py-4 space-y-1 pb-6">
            <a href="{{ url('/') }}#inicio" class="block rounded-lg px-3 py-2 text-sm font-semibold text-white/90 hover:bg-white/10">{{ __('brand.nav_home') }}</a>
            <a href="{{ route('tracking.search') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-white/90 hover:bg-white/10">{{ __('brand.nav_tracking') }}</a>
            <a href="{{ url('/') }}#servicios" class="block rounded-lg px-3 py-2 text-sm font-semibold text-white/90 hover:bg-white/10">{{ __('brand.nav_services') }}</a>
            <a href="{{ url('/') }}#contacto" class="block rounded-lg px-3 py-2 text-sm font-semibold text-white/90 hover:bg-white/10">{{ __('brand.nav_contact') }}</a>
            @auth
                <a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-white/90 hover:bg-white/10">{{ __('Dashboard') }}</a>
                <a href="{{ route('shipments.index') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-white/90 hover:bg-white/10">{{ __('shipments.menu') }}</a>
                @isset($navOrganizations)
                    @if ($navOrganizations->count() > 1)
                        <form method="POST" action="{{ route('organization.switch') }}" class="px-3 pt-2">
                            @csrf
                            <label for="nav_organization_id_m" class="block text-xs text-white/60">{{ __('Organización activa') }}</label>
                            <select id="nav_organization_id_m" name="organization_id" onchange="this.form.submit()" class="mt-1 w-full text-sm border-white/20 bg-white/10 text-white rounded-lg shadow-inner">
                                @foreach ($navOrganizations as $org)
                                    <option value="{{ $org->id }}" class="text-slate-900" @selected(isset($currentOrganization) && $currentOrganization->id === $org->id)>{{ $org->name }}</option>
                                @endforeach
                            </select>
                        </form>
                    @elseif (isset($currentOrganization) && $currentOrganization)
                        <div class="px-3 py-2 text-xs text-white/60">{{ $currentOrganization->name }}</div>
                    @endif
                @endisset
                <div class="border-t border-white/10 pt-3 mt-3 space-y-1">
                    <div class="px-3 text-sm font-semibold text-white">{{ Auth::user()->name }}</div>
                    <a href="{{ route('profile.edit') }}" class="block rounded-lg px-3 py-2 text-sm text-white/85 hover:bg-white/10">{{ __('Profile') }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left rounded-lg px-3 py-2 text-sm text-white/85 hover:bg-white/10">{{ __('Log Out') }}</button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="block mt-2 rounded-xl bg-white px-3 py-3 text-center text-sm font-bold text-brand-800 shadow-lg">{{ __('brand.nav_login') }}</a>
            @endauth
        </div>
    </div>
</header>
