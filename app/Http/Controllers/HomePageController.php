<?php

namespace App\Http\Controllers;

use Inertia\Response;

class HomePageController extends Controller
{
    public function index(): Response
    {
        $buttonConfig = [
            [
                'providerName' => 'Spotify',
                'label' => session('spotifyAccessToken') ? 'View Spotify Playlist' : 'Connect to Spotify',
                'href' => session('spotifyAccessToken') ? route('spotify.playlist') : route('spotify.authorize'),
            ],
            [
                'providerName' => 'Youtube Music',
                'label' => session('ytMusicAccessToken') ? 'View Youtube Music Playlist' : 'Connect to Youtube Music',
                'href' => session('ytMusicRefreshToken') ? route('youtube.playlist') : route('youtube.authorize'),
            ],
        ];

        return inertia('Home/Index',
            [
                'buttonConfig' => $buttonConfig,
            ]);
    }
}
