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
                'label' => 'Connect to Youtube Music',
                'href' => route('youtube.playlist'),
            ],
        ];

        return inertia('Home/Index',
            [
                'buttonConfig' => $buttonConfig,
            ]);
    }
}
