<footer class="mt-auto border-t border-slate-200 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 gap-10 md:grid-cols-3">
            <div class="space-y-3">
                <x-brand.logo />
                <p class="text-sm text-slate-600 max-w-xs">{{ __('brand.footer_pitch') }}</p>
            </div>

            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-3">{{ __('brand.nav_contact') }}</p>
                <ul class="space-y-2 text-sm text-slate-600">
                    <li><a href="{{ url('/') }}#inicio" class="hover:text-brand-600">{{ __('brand.nav_home') }}</a></li>
                    <li><a href="{{ route('tracking.search') }}" class="hover:text-brand-600">{{ __('brand.nav_tracking') }}</a></li>
                    <li><a href="{{ url('/') }}#contacto" class="hover:text-brand-600">{{ __('brand.nav_contact') }}</a></li>
                </ul>
            </div>

            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-3">{{ __('brand.contact_title') }}</p>
                <ul class="space-y-2 text-sm text-slate-600">
                    <li><span class="font-medium text-slate-700">{{ __('brand.contact_phone_label') }}:</span> {{ __('brand.contact_phone_value') }}</li>
                    <li><span class="font-medium text-slate-700">{{ __('brand.contact_email_label') }}:</span> {{ __('brand.contact_email_value') }}</li>
                </ul>
                <p class="mt-4 text-xs text-slate-500">{{ __('brand.social_hint') }}</p>
                <div class="mt-2 flex gap-3">
                    <a href="#" class="flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-400 hover:border-brand-300 hover:text-brand-600 transition" title="Facebook (placeholder)">
                        <span class="sr-only">Facebook</span>
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12a10 10 0 10-11.5 9.95v-7.05H7V12h3.5V9.5c0-3.45 2-5.5 5.16-5.5 1.5 0 3.06.27 3.06.27v3.36h-1.73c-1.7 0-2.23 1.06-2.23 2.15V12h3.79l-.61 3.9h-3.18V22A10 10 0 0022 12z"/></svg>
                    </a>
                    <a href="#" class="flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-400 hover:border-brand-300 hover:text-brand-600 transition" title="Instagram (placeholder)">
                        <span class="sr-only">Instagram</span>
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M7.75 2h8.5A5.75 5.75 0 0022 7.75v8.5A5.75 5.75 0 0016.25 22h-8.5A5.75 5.75 0 002 16.25v-8.5A5.75 5.75 0 017.75 2zm0 1.5A4.25 4.25 0 003.5 7.75v8.5A4.25 4.25 0 007.75 20.5h8.5a4.25 4.25 0 004.25-4.25v-8.5A4.25 4.25 0 0016.25 3.5h-8.5zM12 7a5 5 0 110 10 5 5 0 010-10zm0 1.5a3.5 3.5 0 100 7 3.5 3.5 0 000-7zm5.25-3.25a1 1 0 110 2 1 1 0 010-2z"/></svg>
                    </a>
                    <a href="#" class="flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-400 hover:border-brand-300 hover:text-brand-600 transition" title="LinkedIn (placeholder)">
                        <span class="sr-only">LinkedIn</span>
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M4.98 3.5C4.98 4.88 3.86 6 2.5 6S0 4.88 0 3.5 1.12 1 2.5 1 4.98 2.12 4.98 3.5zM.5 23.5h4V8h-4v15.5zM8.5 8h3.8v2.05h.05c.53-1 1.82-2.05 3.74-2.05 4 0 4.74 2.62 4.74 6.03V23.5h-4v-8.2c0-2-.04-4.57-2.79-4.57-2.79 0-3.22 2.18-3.22 4.43V23.5h-4V8z"/></svg>
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-10 pt-8 border-t border-slate-200 space-y-3 text-center md:text-left md:flex md:items-center md:justify-between md:gap-4">
            <p class="text-xs text-slate-500">{{ __('brand.copyright_line', ['year' => 2026]) }}</p>
            <p class="text-xs text-slate-500">
                {!! __('brand.developer_credit', ['developer' => '<a href="https://rojastech.com.co" target="_blank" rel="noopener noreferrer" class="font-semibold text-brand-600 hover:text-brand-700 underline decoration-brand-300 underline-offset-2">' . e(__('brand.developer_name')) . '</a>']) !!}
            </p>
        </div>
    </div>
</footer>
