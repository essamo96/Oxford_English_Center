<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use App\Models\Settings;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Share settings with all email views
        View::composer('emails.*', function ($view) {
            $settings = Settings::find(1);
            $view->with('mysettings', $settings);
        });

        // Apply SMTP settings from database if they exist
        try {
            $settings = Settings::find(1);
            if ($settings && $settings->smtp_host) {
                Config::set('mail.mailers.smtp.host', $settings->smtp_host);
                Config::set('mail.mailers.smtp.port', $settings->smtp_port);
                Config::set('mail.mailers.smtp.encryption', $settings->smtp_crypto);
                Config::set('mail.mailers.smtp.username', $settings->email);
                Config::set('mail.mailers.smtp.password', $settings->email_password);
                Config::set('mail.from.address', $settings->email);
                Config::set('mail.from.name', $settings->title);
            }
        } catch (\Exception $e) {
            // Silently fail if DB is not ready or table doesn't exist
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
