<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Inertia\Response;

class SpotifyController extends Controller
{
    public function index(): Response
    {
        $userName = $this->getProfile()["display_name"];
        $playlists = array_map(fn ($playlist) => $playlist['name'], $this->getPlaylists());

        return inertia('Spotify/Index', [
            'userName' => $userName,
            'playlists' => $playlists
        ]);
    }

    public function getProfile()
    {
        return Http::withToken(session('spotifyAccessToken'))->get('https://api.spotify.com/v1/me')->json();
    }

    public function getPlaylists()
    {
        return Http::withToken(session('spotifyAccessToken'))->get('https://api.spotify.com/v1/me/playlists')['items'];
    }
}
