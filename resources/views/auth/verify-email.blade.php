<x-auth-layout>
    <div class="rounded-3xl bg-white p-9 sm:p-11 shadow-2xl shadow-black/40 border border-white/60 ring-1 ring-slate-900/5">
        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-widest text-brand-600 mb-3">{{ __('brand.name') }}</p>
            <h1 class="font-display text-3xl font-extrabold text-slate-900 tracking-tight">{{ __('auth.verify_email_heading') }}</h1>
            <p class="mt-4 text-sm text-slate-600 leading-relaxed">
                {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
            </p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-6 rounded-2xl bg-emerald-50 border border-emerald-100 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </div>
        @endif

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="inline-flex justify-center rounded-2xl bg-brand-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-brand-600/30 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition">
                    {{ __('Resend Verification Email') }}
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm font-semibold text-slate-500 hover:text-brand-700">
                    {{ __('Log Out') }}
                </button>
            </form>
        </div>
    </div>
</x-auth-layout>
