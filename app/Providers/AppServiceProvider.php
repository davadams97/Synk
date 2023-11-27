<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

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
        // TODO: check for refresh tokens later
        Http::macro('spotify', function () {
            return Http::withToken(session('spotifyAccessToken'))->baseUrl('https://api.spotify.com/v1');
        });
    }
}
