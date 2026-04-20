<x-auth-layout>
    <div class="rounded-3xl bg-white p-9 sm:p-11 shadow-2xl shadow-black/40 border border-white/60 ring-1 ring-slate-900/5">
        <div class="text-center mb-10">
            @if (brand_logo_asset())
                <div class="flex justify-center mb-5 group">
                    <img src="{{ brand_logo_asset() }}" alt="{{ __('brand.name') }}" class="h-8 w-auto max-w-[12rem] object-contain mx-auto transition duration-300 ease-out group-hover:scale-105 group-hover:drop-shadow-[0_4px_18px_rgba(37,99,235,0.45)]" />
                </div>
            @else
                <p class="text-xs font-bold uppercase tracking-widest text-brand-600 mb-3">{{ __('brand.name') }}</p>
            @endif
            <h1 class="font-display text-4xl sm:text-[2.75rem] font-extrabold text-slate-900 tracking-tight leading-tight">
                {{ __('auth.login_heading') }}
            </h1>
            <p class="mt-4 text-base text-slate-600">{{ __('auth.login_subheading') }}</p>
        </div>

        <x-auth-session-status class="mb-6" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Email')" class="text-slate-800 font-semibold text-sm" />
                <x-text-input id="email" class="block mt-2 w-full rounded-2xl border-slate-200 bg-slate-50/80 py-3.5 px-4 text-base shadow-inner focus:border-brand-500 focus:ring-brand-500" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('Password')" class="text-slate-800 font-semibold text-sm" />
                <x-text-input id="password" class="block mt-2 w-full rounded-2xl border-slate-200 bg-slate-50/80 py-3.5 px-4 text-base shadow-inner focus:border-brand-500 focus:ring-brand-500"
                    type="password"
                    name="password"
                    required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded-md border-slate-300 text-brand-600 shadow-sm focus:ring-brand-500" name="remember">
                    <span class="ml-2 text-sm text-slate-600">{{ __('Remember me') }}</span>
                </label>
                @if (Route::has('password.request'))
                    <a class="text-sm font-semibold text-brand-600 hover:text-brand-800" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <button type="submit" class="w-full flex justify-center items-center gap-2 rounded-2xl bg-brand-600 px-4 py-4 text-lg font-bold text-white shadow-xl shadow-brand-600/35 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition">
                {{ __('auth.login_submit') }}
            </button>
        </form>

        @if (Route::has('register'))
            <p class="mt-10 text-center text-sm text-slate-600">
                {{ __('auth.no_account') }}
                <a href="{{ route('register') }}" class="font-bold text-brand-600 hover:text-brand-800">{{ __('Register') }}</a>
            </p>
        @endif

        <p class="mt-8 text-center">
            <a href="{{ url('/') }}" class="text-sm font-medium text-slate-500 hover:text-brand-700">{{ __('auth.back_home') }}</a>
        </p>
    </div>
</x-auth-layout>
