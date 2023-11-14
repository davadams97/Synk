<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SpotifyService {
    public function getProfile()
    {
        return Http::withToken(session('spotifyAccessToken'))->get('https://api.spotify.com/v1/me')->json();
    }

    public function getPlaylists()
    {
        return Http::withToken(session('spotifyAccessToken'))->get('https://api.spotify.com/v1/me/playlists')['items'];
    }
}