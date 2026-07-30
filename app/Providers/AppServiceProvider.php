<?php

namespace App\Providers;

use App\Channels\TweetSmsChannel;
use App\Services\SmsService;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Schema::defaultStringLength(191);

        // Safe route helper: returns '#' instead of throwing if route name is missing.
        // Usage in views: {{ safe_route('some.route') }}
        // The guard has to name the function as declared — this file is namespaced, so the
        // declaration below is App\Providers\safe_route, while function_exists('safe_route')
        // asks about the *global* one and always answers false. That let a second boot() in
        // the same process (any test run with more than one test) fatal on redeclaration.
        if (!function_exists(__NAMESPACE__ . '\\safe_route')) {
            function safe_route(string $name, $parameters = [], bool $absolute = true): string
            {
                try {
                    return Route::has($name) ? route($name, $parameters, $absolute) : '#';
                } catch (\Throwable $e) {
                    return '#';
                }
            }
        }

        // View Composer: inject per-user sidebar settings into master layout.
        // Runs at view-render time (after auth middleware), so auth() is available.
        View::composer(['admin.layout.master', 'admin_v2.layout.master'], function ($view) {
            $siteSettings = \Cache::remember('site_settings', 300,
                fn() => \App\Models\Settings::find(1)
            );

            $authUser = auth()->guard('admin')->user();

            // Branch context — resolved here (after auth middleware) not in controller constructor
            $branchContext = app(\App\Services\BranchContext::class);
            $branchId = $branchContext->getId();
            $view->with('isBranchScoped', $branchContext->isScoped());
            $view->with('activeBranch',   $branchContext->getBranch());

            // Notifications scoped to the user's branch (null = super admin sees all)
            $view->with(\App\Support\NotifyCounts::allForView($branchId));

            if ($authUser) {
                // Reload fresh from DB to get latest sidebar preferences
                $fresh = \App\Models\User::select(
                    'sidebar_layout', 'sidebar_bg_color',
                    'sidebar_text_color', 'sidebar_active_color'
                )->find($authUser->id);

                if ($fresh) {
                    $effective = clone $siteSettings;
                    if ($fresh->sidebar_layout)       $effective->sidebar_layout       = $fresh->sidebar_layout;
                    if ($fresh->sidebar_bg_color)     $effective->sidebar_bg_color     = $fresh->sidebar_bg_color;
                    if ($fresh->sidebar_text_color)   $effective->sidebar_text_color   = $fresh->sidebar_text_color;
                    if ($fresh->sidebar_active_color) $effective->sidebar_active_color = $fresh->sidebar_active_color;
                    $view->with('siteSettings', $effective);
                    return;
                }
            }

            $view->with('siteSettings', $siteSettings);
        });
    }


    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\App\Services\BranchContext::class);
        $this->app->singleton(SmsService::class);

        // Register the 'tweetsms' driver with Laravel's notification system.
        $this->app->resolving(ChannelManager::class, function (ChannelManager $manager) {
            $manager->extend('tweetsms', fn () => new TweetSmsChannel(app(SmsService::class)));
        });
    }
}
