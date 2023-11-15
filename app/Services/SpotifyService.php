<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SpotifyService {
    public function getProfile()
    {
        return Http::spotify()->get('/')->json();
    }

    public function getPlaylists()
    {
        return Http::spotify()->get('/playlists')['items'];
    }
}