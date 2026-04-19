<x-auth-layout>
    <div class="rounded-3xl bg-white p-9 sm:p-11 shadow-2xl shadow-black/40 border border-white/60 ring-1 ring-slate-900/5">
        <div class="text-center mb-8">
            <p class="text-xs font-bold uppercase tracking-widest text-brand-600 mb-3">{{ __('brand.name') }}</p>
            <h1 class="font-display text-3xl font-extrabold text-slate-900 tracking-tight">{{ __('Reset Password') }}</h1>
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <x-input-label for="email" :value="__('Email')" class="text-slate-800 font-semibold text-sm" />
                <x-text-input id="email" class="block mt-2 w-full rounded-2xl border-slate-200 bg-slate-50/80 py-3.5 px-4 text-base shadow-inner focus:border-brand-500 focus:ring-brand-500" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('Password')" class="text-slate-800 font-semibold text-sm" />
                <x-text-input id="password" class="block mt-2 w-full rounded-2xl border-slate-200 bg-slate-50/80 py-3.5 px-4 text-base shadow-inner focus:border-brand-500 focus:ring-brand-500" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-slate-800 font-semibold text-sm" />
                <x-text-input id="password_confirmation" class="block mt-2 w-full rounded-2xl border-slate-200 bg-slate-50/80 py-3.5 px-4 text-base shadow-inner focus:border-brand-500 focus:ring-brand-500"
                    type="password"
                    name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <button type="submit" class="w-full flex justify-center rounded-2xl bg-brand-600 px-4 py-4 text-base font-bold text-white shadow-xl shadow-brand-600/35 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition">
                {{ __('Reset Password') }}
            </button>
        </form>
    </div>
</x-auth-layout>
