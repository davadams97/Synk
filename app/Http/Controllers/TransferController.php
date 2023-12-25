<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Response;

class TransferController extends Controller
{
    public function source(): Response
    {
        $buttonConfig = [
            [
                'providerName' => 'spotify',
                'logo' => Storage::url('spotify_logo.png'),
                'alt' => 'spotify_logo',
                'isConnected' => session('spotifyAccessToken'),
                'href' => session('spotifyAccessToken') ? 'transfer.target' : 'spotify.authorize',
            ],
            [
                'providerName' => 'ytmusic',
                'logo' => Storage::url('youtube_music_logo.png'),
                'alt' => 'youtube_music_logo',
                'isConnected' => session('ytMusicAccessToken'),
                'href' => session('ytMusicRefreshToken') ? 'transfer.target' : 'youtube.authorize',
            ],
        ];

        $header = 'Where would you like to transfer from?';

        return inertia('Transfer/Show',
            [
                'buttonConfig' => $buttonConfig,
                'header' => $header
            ]);
    }

    public function target(Request $request): Response
    {
        $targetProvider = $request['provider'];

        $buttonConfig = [
            [
                'providerName' => 'spotify',
                'logo' => Storage::url('spotify_logo.png'),
                'alt' => 'spotify_logo',
                'isConnected' => session('spotifyAccessToken'),
                'href' => session('spotifyAccessToken') ? '' : 'spotify.authorize',
            ],
            [
                'providerName' => 'ytmusic',
                'logo' => Storage::url('youtube_music_logo.png'),
                'alt' => 'youtube_music_logo',
                'isConnected' => session('ytMusicAccessToken'),
                'href' => session('ytMusicRefreshToken') ? '' : 'youtube.authorize',
            ],
        ];

        $header = 'Where would you like to transfer to?';

        return inertia('Transfer/Show',
            [
                'buttonConfig' => $buttonConfig,
                'header' => $header
            ]);
    }
}
