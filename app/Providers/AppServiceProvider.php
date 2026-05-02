<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use App\Models\Ticket;
use App\Observers\TicketObserver;

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
        Ticket::observe(TicketObserver::class);

        $appUrl = config('app.url');

        if ($appUrl) {
            URL::forceRootUrl($appUrl);

            if (str_starts_with($appUrl, 'https://') || $this->app->environment('production')) {
                URL::forceScheme('https');
            }
        }
    }
}
