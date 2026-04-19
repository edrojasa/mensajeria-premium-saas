<header class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/90 backdrop-blur-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-4">
            <a href="{{ url('/') }}" class="flex items-center gap-2 text-slate-900 hover:text-brand-700 transition">
                <x-brand.logo class="!h-9" />
            </a>

            <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-600">
                <a href="{{ url('/') }}#inicio" class="hover:text-brand-600 transition">{{ __('brand.nav_home') }}</a>
                <a href="{{ route('tracking.search') }}" class="hover:text-brand-600 transition">{{ __('brand.nav_tracking') }}</a>
                <a href="{{ url('/') }}#servicios" class="hover:text-brand-600 transition">{{ __('brand.nav_services') }}</a>
                <a href="{{ url('/') }}#contacto" class="hover:text-brand-600 transition">{{ __('brand.nav_contact') }}</a>
            </nav>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="hidden sm:inline-flex text-sm font-medium text-slate-600 hover:text-brand-600">{{ __('Dashboard') }}</a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition">
                        {{ __('brand.nav_login') }}
                    </a>
                @endauth

                <button type="button" class="md:hidden inline-flex items-center justify-center rounded-lg border border-slate-200 p-2 text-slate-600 hover:bg-slate-50" onclick="document.getElementById('mobile-nav').classList.toggle('hidden')" aria-expanded="false" aria-controls="mobile-nav">
                    <span class="sr-only">Menú</span>
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>

        <div id="mobile-nav" class="hidden md:hidden border-t border-slate-100 py-4 space-y-2">
            <a href="{{ url('/') }}#inicio" class="block py-2 text-sm font-medium text-slate-700">{{ __('brand.nav_home') }}</a>
            <a href="{{ route('tracking.search') }}" class="block py-2 text-sm font-medium text-slate-700">{{ __('brand.nav_tracking') }}</a>
            <a href="{{ url('/') }}#servicios" class="block py-2 text-sm font-medium text-slate-700">{{ __('brand.nav_services') }}</a>
            <a href="{{ url('/') }}#contacto" class="block py-2 text-sm font-medium text-slate-700">{{ __('brand.nav_contact') }}</a>
            @guest
                <a href="{{ route('login') }}" class="block py-2 text-sm font-semibold text-brand-600">{{ __('brand.nav_login') }}</a>
            @else
                <a href="{{ route('dashboard') }}" class="block py-2 text-sm font-semibold text-brand-600">{{ __('Dashboard') }}</a>
            @endguest
        </div>
    </div>
</header>
