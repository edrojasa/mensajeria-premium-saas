<?php

namespace App\Providers;

use App\Support\TenantManager;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(TenantManager::class, function ($app) {
            return new TenantManager($app->make(Session::class));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer(['layouts.app', 'layouts.guest', 'components.marketing-layout', 'partials.site-header', 'dashboard', 'profile.edit', 'shipments.show'], function ($view) {
            if (! Auth::check()) {
                return;
            }

            $tenantManager = app(TenantManager::class);
            $tenantManager->synchronizeSessionOrganization(Auth::user());

            $view->with([
                'currentOrganization' => $tenantManager->currentOrganization(),
                'navOrganizations' => Auth::user()->organizations()->orderBy('organizations.name')->get(),
            ]);
        });
    }
}
