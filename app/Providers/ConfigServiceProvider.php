<?php

namespace App\Providers;

use App\Models\Site_setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Don't run this during console/migrations to avoid chicken-and-egg problems
        if ($this->app->runningInConsole()) {
            return;
        }

        try {
            if (Schema::hasTable('site_settings')) {
                $settings = Site_setting::first();

                if ($settings) {
                    // Site Name & Branding
                    if ($settings->name) {
                        Config::set('app.name', $settings->name);
                    }

                    if ($settings->app_url) {
                        Config::set('app.url', $settings->app_url);
                    }

                    if ($settings->app_debug !== null) {
                        Config::set('app.debug', (bool) $settings->app_debug);
                    }

                    // SMTP / Mail Configuration
                    if ($settings->smtp_host) {
                        Config::set('mail.mailers.smtp.host', $settings->smtp_host);
                        Config::set('mail.mailers.smtp.port', $settings->smtp_port ?? 587);
                        Config::set('mail.mailers.smtp.username', $settings->smtp_user);
                        Config::set('mail.mailers.smtp.password', $settings->smtp_pass);
                        Config::set('mail.mailers.smtp.encryption', $settings->smtp_encryption ?? 'tls');
                        Config::set('mail.from.address', $settings->mail_from_address ?? $settings->email);
                        Config::set('mail.from.name', $settings->name);
                    }

                    // Pusher Configuration
                    if ($settings->pusher_app_id) {
                        Config::set('broadcasting.connections.pusher.key', $settings->pusher_app_key);
                        Config::set('broadcasting.connections.pusher.secret', $settings->pusher_app_secret);
                        Config::set('broadcasting.connections.pusher.app_id', $settings->pusher_app_id);
                        Config::set('broadcasting.connections.pusher.options.cluster', $settings->pusher_app_cluster ?? 'mt1');
                    }
                }
            }
        } catch (\Exception $e) {
            // Silently fail if DB not ready
        }
    }
}
