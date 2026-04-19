<x-auth-layout>
    <div class="rounded-3xl bg-white p-9 sm:p-11 shadow-2xl shadow-black/40 border border-white/60 ring-1 ring-slate-900/5">
        <div class="text-center mb-10">
            <p class="text-xs font-bold uppercase tracking-widest text-brand-600 mb-3">{{ __('brand.name') }}</p>
            <h1 class="font-display text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">{{ __('Register') }}</h1>
            <p class="mt-4 text-base text-slate-600">{{ __('auth.register_subheading') }}</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="name" :value="__('Name')" class="text-slate-800 font-semibold text-sm" />
                <x-text-input id="name" class="block mt-2 w-full rounded-2xl border-slate-200 bg-slate-50/80 py-3.5 px-4 text-base shadow-inner focus:border-brand-500 focus:ring-brand-500" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="organization_name" :value="__('Nombre de la empresa / organización')" class="text-slate-800 font-semibold text-sm" />
                <x-text-input id="organization_name" class="block mt-2 w-full rounded-2xl border-slate-200 bg-slate-50/80 py-3.5 px-4 text-base shadow-inner focus:border-brand-500 focus:ring-brand-500" type="text" name="organization_name" :value="old('organization_name')" required autocomplete="organization" />
                <x-input-error :messages="$errors->get('organization_name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email')" class="text-slate-800 font-semibold text-sm" />
                <x-text-input id="email" class="block mt-2 w-full rounded-2xl border-slate-200 bg-slate-50/80 py-3.5 px-4 text-base shadow-inner focus:border-brand-500 focus:ring-brand-500" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('Password')" class="text-slate-800 font-semibold text-sm" />
                <x-text-input id="password" class="block mt-2 w-full rounded-2xl border-slate-200 bg-slate-50/80 py-3.5 px-4 text-base shadow-inner focus:border-brand-500 focus:ring-brand-500"
                    type="password"
                    name="password"
                    required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-slate-800 font-semibold text-sm" />
                <x-text-input id="password_confirmation" class="block mt-2 w-full rounded-2xl border-slate-200 bg-slate-50/80 py-3.5 px-4 text-base shadow-inner focus:border-brand-500 focus:ring-brand-500"
                    type="password"
                    name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <button type="submit" class="w-full mt-2 flex justify-center rounded-2xl bg-brand-600 px-4 py-4 text-lg font-bold text-white shadow-xl shadow-brand-600/35 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition">
                {{ __('Register') }}
            </button>
        </form>

        <p class="mt-10 text-center text-sm text-slate-600">
            <a href="{{ route('login') }}" class="font-bold text-brand-600 hover:text-brand-800">{{ __('Already registered?') }}</a>
        </p>
    </div>
</x-auth-layout>
