<?php

namespace App\Providers;

use App\Console\Commands\SendTestMailCommand;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SendTestMailCommand::class,
            ]);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('admin-login', function (Request $request) {
            $nip = (string) $request->input('nip');

            return Limit::perMinute(5)->by($nip . '|' . $request->ip());
        });
    }
}
