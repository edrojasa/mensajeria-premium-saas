@php
    $logoHref = Auth::check() ? route('dashboard') : url('/');
    // Texto claro forzado: el layout usa body text-slate-900; sin !important los enlaces pueden quedar oscuros hasta :hover.
    $navLink = 'inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold transition whitespace-nowrap';
    $navIdle = '!text-white/85 hover:!text-white hover:bg-white/10';
    $navActive = '!text-white bg-white/15 shadow-inner';
    $operationNavActive = auth()->check() && Auth::user()->isMessenger()
        ? request()->routeIs('dashboard', 'courier.shipments.*')
        : request()->routeIs('dashboard', 'shipments.*', 'customers.*', 'users.*', 'logs.*', 'operations.messengers.report*');
    $financeNavActive = request()->routeIs('financial.reports', 'financial.receivables', 'service-rates.*');
@endphp

<header x-data="{ mobileOpen: false, operationOpen: false, financeOpen: false }" class="site-header sticky top-0 z-50 border-b border-white/10 shadow-xl shadow-black/25">
    <div class="absolute inset-0 bg-slate-950" aria-hidden="true"></div>
    <div
        class="absolute inset-0 bg-cover bg-center opacity-35 mix-blend-soft-light"
        style="background-image: url('{{ asset('images/hero/logistics-bg.jpg') }}')"
        aria-hidden="true"
    ></div>
    <div class="absolute inset-0 bg-gradient-to-r from-brand-950/95 via-slate-950/92 to-brand-950/95" aria-hidden="true"></div>
    <div class="absolute inset-0 bg-[linear-gradient(to_bottom,rgba(255,255,255,0.06),transparent_40%)] pointer-events-none" aria-hidden="true"></div>

    <div class="relative max-w-7xl mx-auto px-5 sm:px-8 lg:px-8">
        <div class="site-header__bar flex min-h-[4.25rem] md:min-h-[5rem] items-center justify-between gap-4 py-2">
            <div class="flex items-center gap-6 lg:gap-10 min-w-0 flex-1">
                <a href="{{ $logoHref }}" class="shrink-0 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-400 focus:ring-offset-2 focus:ring-offset-slate-950 py-1">
                    <x-brand.logo variant="light" size="nav" />
                </a>

                <nav class="hidden md:flex flex-wrap items-center gap-0.5 lg:gap-1 min-w-0">
                    <a href="{{ url('/') }}#inicio" class="{{ $navLink }} {{ $navIdle }}">{{ __('brand.nav_home') }}</a>
                    <a href="{{ route('tracking.search') }}" class="{{ $navLink }} {{ request()->routeIs('tracking.search') ? $navActive : $navIdle }}">{{ __('brand.nav_tracking') }}</a>
                    <a href="{{ url('/') }}#servicios" class="{{ $navLink }} {{ $navIdle }}">{{ __('brand.nav_services') }}</a>
                    <a href="{{ url('/') }}#contacto" class="{{ $navLink }} {{ $navIdle }}">{{ __('brand.nav_contact') }}</a>

                    @auth
                        @if (Auth::user()->isMessenger())
                            <div class="hidden md:block">
                                <x-dropdown align="left" width="56">
                                    <x-slot name="trigger">
                                        <button type="button" class="site-header__trigger {{ $navLink }} {{ $operationNavActive ? $navActive : $navIdle }}">
                                            {{ __('brand.nav_operations') }}
                                            <svg class="h-4 w-4 shrink-0 text-current opacity-90" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                        </button>
                                    </x-slot>
                                    <x-slot name="content">
                                        <x-dropdown-link href="{{ route('dashboard') }}">{{ __('Dashboard') }}</x-dropdown-link>
                                        <x-dropdown-link href="{{ route('courier.shipments.index') }}">{{ __('shipments.my_shipments_menu') }}</x-dropdown-link>
                                    </x-slot>
                                </x-dropdown>
                            </div>
                        @else
                            <div class="hidden md:block">
                                <x-dropdown align="left" width="56">
                                    <x-slot name="trigger">
                                        <button type="button" class="site-header__trigger {{ $navLink }} {{ $operationNavActive ? $navActive : $navIdle }}">
                                            {{ __('brand.nav_operations') }}
                                            <svg class="h-4 w-4 shrink-0 text-current opacity-90" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                        </button>
                                    </x-slot>
                                    <x-slot name="content">
                                        <x-dropdown-link href="{{ route('dashboard') }}">{{ __('Dashboard') }}</x-dropdown-link>
                                        <x-dropdown-link href="{{ route('shipments.index') }}">{{ __('shipments.menu') }}</x-dropdown-link>
                                        @if (Auth::user()->canManageCustomers())
                                            <x-dropdown-link href="{{ route('customers.index') }}">{{ __('shipments.customers_menu') }}</x-dropdown-link>
                                        @endif
                                        @if (Auth::user()->canViewOrganizationUsers())
                                            <x-dropdown-link href="{{ route('users.index') }}">{{ __('shipments.users_menu') }}</x-dropdown-link>
                                        @endif
                                        @if (Auth::user()->canViewAuditLogs())
                                            <x-dropdown-link href="{{ route('logs.index') }}">{{ __('logs.menu') }}</x-dropdown-link>
                                        @endif
                                        @if (Auth::user()->canOperateLogistics())
                                            <x-dropdown-link href="{{ route('operations.messengers.report') }}">Reporte de Mensajeros</x-dropdown-link>
                                        @endif
                                    </x-slot>
                                </x-dropdown>
                            </div>

                            @if (Auth::user()->canAccessFinancialModule())
                                <div class="hidden md:block">
                                    <x-dropdown align="left" width="56">
                                        <x-slot name="trigger">
                                            <button type="button" class="site-header__trigger {{ $navLink }} {{ $financeNavActive ? $navActive : $navIdle }}">
                                                {{ __('finance.menu_main') }}
                                                <svg class="h-4 w-4 shrink-0 text-current opacity-90" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                            </button>
                                        </x-slot>
                                        <x-slot name="content">
                                            <x-dropdown-link href="{{ route('financial.receivables') }}">{{ __('finance.menu_receivables') }}</x-dropdown-link>
                                            <x-dropdown-link href="{{ route('service-rates.index') }}">{{ __('finance.menu_rates') }}</x-dropdown-link>
                                            <x-dropdown-link href="{{ route('financial.reports') }}">{{ __('finance.menu_reports') }}</x-dropdown-link>
                                        </x-slot>
                                    </x-dropdown>
                                </div>
                            @endif
                        @endif
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
                                <select id="nav_organization_id" name="organization_id" onchange="this.form.submit()" class="text-[0.9375rem] max-w-[12rem] border-white/20 bg-white/10 text-white placeholder-white/60 focus:border-brand-400 focus:ring-brand-400 rounded-xl shadow-inner py-2 px-3">
                                    @foreach ($navOrganizations as $org)
                                        <option value="{{ $org->id }}" class="text-slate-900" @selected(isset($currentOrganization) && $currentOrganization->id === $org->id)>
                                            {{ $org->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        @elseif (isset($currentOrganization) && $currentOrganization)
                            <span class="hidden xl:inline text-[0.8125rem] text-white/65 truncate max-w-[11rem]" title="{{ $currentOrganization->name }}">{{ $currentOrganization->name }}</span>
                        @endif
                    @endisset

                    <div class="hidden sm:flex sm:items-center">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button type="button" class="site-header__trigger inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-semibold transition whitespace-nowrap max-w-[14rem] {{ $navIdle }}">
                                    <span class="truncate">{{ Auth::user()->name }}</span>
                                    <svg class="h-4 w-4 shrink-0 text-current opacity-90" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
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
                    <a href="{{ route('login') }}" class="site-header__login inline-flex min-h-[2.75rem] items-center justify-center rounded-xl bg-white px-5 py-2.5 text-[0.9375rem] font-bold text-brand-800 shadow-lg shadow-black/25 hover:bg-brand-50 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:ring-offset-2 focus:ring-offset-slate-950 transition">
                        {{ __('brand.nav_login') }}
                    </a>
                @endauth

                <button type="button" @click="mobileOpen = ! mobileOpen" class="site-header__trigger md:hidden inline-flex min-h-[2.75rem] min-w-[2.75rem] items-center justify-center rounded-xl border border-white/15 bg-white/10 p-2 hover:bg-white/15" :aria-expanded="mobileOpen">
                    <span class="sr-only">Menú</span>
                    <svg x-show="!mobileOpen" class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="mobileOpen" x-cloak class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <div x-show="mobileOpen" x-cloak x-transition class="site-header__mobile md:hidden border-t border-white/12 py-4 space-y-1 pb-6">
            <a href="{{ url('/') }}#inicio" class="block rounded-xl px-3 py-2.5 text-[0.9375rem] font-semibold hover:bg-white/10">{{ __('brand.nav_home') }}</a>
            <a href="{{ route('tracking.search') }}" class="block rounded-xl px-3 py-2.5 text-[0.9375rem] font-semibold hover:bg-white/10">{{ __('brand.nav_tracking') }}</a>
            <a href="{{ url('/') }}#servicios" class="block rounded-xl px-3 py-2.5 text-[0.9375rem] font-semibold hover:bg-white/10">{{ __('brand.nav_services') }}</a>
            <a href="{{ url('/') }}#contacto" class="block rounded-xl px-3 py-2.5 text-[0.9375rem] font-semibold hover:bg-white/10">{{ __('brand.nav_contact') }}</a>
            @auth
                @if (Auth::user()->isMessenger())
                    <div class="mt-2 rounded-xl border border-white/12 bg-white/5 overflow-hidden">
                        <button type="button" @click="operationOpen = ! operationOpen" class="flex w-full items-center justify-between px-3 py-3 text-left text-[0.9375rem] font-semibold text-white">
                            <span>{{ __('brand.nav_operations') }}</span>
                            <svg class="h-5 w-5 shrink-0 transition" :class="operationOpen ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        </button>
                        <div x-show="operationOpen" x-transition class="border-t border-white/10 bg-black/15 px-3 py-2 space-y-1">
                            <a href="{{ route('dashboard') }}" class="block rounded-lg py-2.5 pl-2 text-sm font-semibold text-white/90 hover:bg-white/10">{{ __('Dashboard') }}</a>
                            <a href="{{ route('courier.shipments.index') }}" class="block rounded-lg py-2.5 pl-2 text-sm font-semibold text-white/90 hover:bg-white/10">{{ __('shipments.my_shipments_menu') }}</a>
                        </div>
                    </div>
                @else
                    <div class="mt-2 rounded-xl border border-white/12 bg-white/5 overflow-hidden">
                        <button type="button" @click="operationOpen = ! operationOpen" class="flex w-full items-center justify-between px-3 py-3 text-left text-[0.9375rem] font-semibold text-white">
                            <span>{{ __('brand.nav_operations') }}</span>
                            <svg class="h-5 w-5 shrink-0 transition" :class="operationOpen ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        </button>
                        <div x-show="operationOpen" x-transition class="border-t border-white/10 bg-black/15 px-3 py-2 space-y-1">
                            <a href="{{ route('dashboard') }}" class="block rounded-lg py-2.5 pl-2 text-sm font-semibold text-white/90 hover:bg-white/10">{{ __('Dashboard') }}</a>
                            <a href="{{ route('shipments.index') }}" class="block rounded-lg py-2.5 pl-2 text-sm font-semibold text-white/90 hover:bg-white/10">{{ __('shipments.menu') }}</a>
                            @if (Auth::user()->canManageCustomers())
                                <a href="{{ route('customers.index') }}" class="block rounded-lg py-2.5 pl-2 text-sm font-semibold text-white/90 hover:bg-white/10">{{ __('shipments.customers_menu') }}</a>
                            @endif
                            @if (Auth::user()->canViewOrganizationUsers())
                                <a href="{{ route('users.index') }}" class="block rounded-lg py-2.5 pl-2 text-sm font-semibold text-white/90 hover:bg-white/10">{{ __('shipments.users_menu') }}</a>
                            @endif
                            @if (Auth::user()->canViewAuditLogs())
                                <a href="{{ route('logs.index') }}" class="block rounded-lg py-2.5 pl-2 text-sm font-semibold text-white/90 hover:bg-white/10">{{ __('logs.menu') }}</a>
                            @endif
                            @if (Auth::user()->canOperateLogistics())
                                <a href="{{ route('operations.messengers.report') }}" class="block rounded-lg py-2.5 pl-2 text-sm font-semibold text-white/90 hover:bg-white/10">Reporte de Mensajeros</a>
                            @endif
                        </div>
                    </div>
                    @if (Auth::user()->canAccessFinancialModule())
                        <div class="mt-2 rounded-xl border border-white/12 bg-white/5 overflow-hidden">
                            <button type="button" @click="financeOpen = ! financeOpen" class="flex w-full items-center justify-between px-3 py-3 text-left text-[0.9375rem] font-semibold text-white">
                                <span>{{ __('finance.menu_main') }}</span>
                                <svg class="h-5 w-5 shrink-0 transition" :class="financeOpen ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                            </button>
                            <div x-show="financeOpen" x-transition class="border-t border-white/10 bg-black/15 px-3 py-2 space-y-1">
                                <a href="{{ route('financial.receivables') }}" class="block rounded-lg py-2.5 pl-2 text-sm font-semibold text-white/90 hover:bg-white/10">{{ __('finance.menu_receivables') }}</a>
                                <a href="{{ route('service-rates.index') }}" class="block rounded-lg py-2.5 pl-2 text-sm font-semibold text-white/90 hover:bg-white/10">{{ __('finance.menu_rates') }}</a>
                                <a href="{{ route('financial.reports') }}" class="block rounded-lg py-2.5 pl-2 text-sm font-semibold text-white/90 hover:bg-white/10">{{ __('finance.menu_reports') }}</a>
                            </div>
                        </div>
                    @endif
                @endif
                @isset($navOrganizations)
                    @if ($navOrganizations->count() > 1)
                        <form method="POST" action="{{ route('organization.switch') }}" class="px-3 pt-3">
                            @csrf
                            <label for="nav_organization_id_m" class="block text-xs text-white/60">{{ __('Organización activa') }}</label>
                            <select id="nav_organization_id_m" name="organization_id" onchange="this.form.submit()" class="mt-1 w-full text-sm border-white/20 bg-white/10 text-white rounded-xl shadow-inner py-2 px-3">
                                @foreach ($navOrganizations as $org)
                                    <option value="{{ $org->id }}" class="text-slate-900" @selected(isset($currentOrganization) && $currentOrganization->id === $org->id)>{{ $org->name }}</option>
                                @endforeach
                            </select>
                        </form>
                    @elseif (isset($currentOrganization) && $currentOrganization)
                        <div class="px-3 py-2 text-xs text-white/60">{{ $currentOrganization->name }}</div>
                    @endif
                @endisset
                <div class="border-t border-white/12 pt-3 mt-3 space-y-1">
                    <div class="px-3 text-[0.9375rem] font-semibold text-white truncate">{{ Auth::user()->name }}</div>
                    <a href="{{ route('profile.edit') }}" class="block rounded-xl px-3 py-2.5 text-sm !text-white/85 hover:!text-white hover:bg-white/10">{{ __('Profile') }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left rounded-xl px-3 py-2.5 text-sm !text-white/85 hover:!text-white hover:bg-white/10">{{ __('Log Out') }}</button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="site-header__login block mt-2 rounded-xl bg-white px-4 py-3 text-center text-[0.9375rem] font-bold text-brand-800 shadow-lg">{{ __('brand.nav_login') }}</a>
            @endauth
        </div>
    </div>
</header>
