<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // ✅ REGISTER THE FILES BINDING
        $this->app->singleton('files', function ($app) {
            return new Filesystem();
        });
    }

    public function boot(): void
    {
        // ✅ FORCE HTTPS
        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }
        URL::forceRootUrl(env('APP_URL'));
    }
}
