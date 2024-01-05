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
            return Http::withToken(session('spotifyAccessToken'))->baseUrl(env('SPOTIFY_API'));
        });

        Http::macro('ytmusic', function () {
            return Http::withToken(session('ytMusicAccessToken'))->baseUrl(env('YOUTUBE_API'));
        });
    }
}
