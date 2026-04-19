<nav x-data="{ open: false }" class="bg-white/95 backdrop-blur border-b border-slate-200 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                        <x-brand.logo />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-8 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('shipments.index')" :active="request()->routeIs('shipments.*')">
                        {{ __('shipments.menu') }}
                    </x-nav-link>
                    <x-nav-link :href="route('tracking.search')" :active="request()->routeIs('tracking.search')">
                        {{ __('brand.nav_tracking') }}
                    </x-nav-link>
                </div>

                @isset($navOrganizations)
                    @if ($navOrganizations->count() > 1)
                        <form method="POST" action="{{ route('organization.switch') }}" class="hidden sm:flex sm:items-center sm:ml-6">
                            @csrf
                            <label class="sr-only" for="nav_organization_id">{{ __('Organización activa') }}</label>
                            <select id="nav_organization_id" name="organization_id" onchange="this.form.submit()" class="text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach ($navOrganizations as $org)
                                    <option value="{{ $org->id }}" @selected(isset($currentOrganization) && $currentOrganization->id === $org->id)>
                                        {{ $org->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @elseif (isset($currentOrganization) && $currentOrganization)
                        <span class="hidden sm:flex sm:items-center sm:ml-6 text-sm text-gray-600 truncate max-w-[12rem]" title="{{ $currentOrganization->name }}">
                            {{ $currentOrganization->name }}
                        </span>
                    @endif
                @endisset
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-slate-600 bg-white hover:text-brand-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('shipments.index')" :active="request()->routeIs('shipments.*')">
                {{ __('shipments.menu') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('tracking.search')" :active="request()->routeIs('tracking.search')">
                {{ __('brand.nav_tracking') }}
            </x-responsive-nav-link>
            @isset($navOrganizations)
                @if ($navOrganizations->count() > 1)
                    <form method="POST" action="{{ route('organization.switch') }}" class="px-4 pt-2">
                        @csrf
                        <label for="nav_organization_id_r" class="block text-xs text-gray-500">{{ __('Organización activa') }}</label>
                        <select id="nav_organization_id_r" name="organization_id" onchange="this.form.submit()" class="mt-1 w-full text-sm border-gray-300 rounded-md shadow-sm">
                            @foreach ($navOrganizations as $org)
                                <option value="{{ $org->id }}" @selected(isset($currentOrganization) && $currentOrganization->id === $org->id)>
                                    {{ $org->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @elseif (isset($currentOrganization) && $currentOrganization)
                    <div class="px-4 pt-2 text-sm text-gray-600">{{ $currentOrganization->name }}</div>
                @endif
            @endisset
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
