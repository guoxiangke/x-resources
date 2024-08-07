<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        date_default_timezone_set('Asia/Shanghai');
        Carbon::macro('inApplicationTimezone', function() {
            return $this->tz(config('app.display_timezone'));
        });
        Carbon::macro('inUserTimezone', function() {
            return $this->tz(auth()->user()?->timezone ?? config('app.display_timezone'));
        });
    }
}
