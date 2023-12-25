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
                'isConnected' => boolval(session('spotifyAccessToken')),
                'href' => session('spotifyAccessToken') ? 'transfer.target' : 'spotify.authorize',
            ],
            [
                'providerName' => 'ytmusic',
                'logo' => Storage::url('youtube_music_logo.png'),
                'alt' => 'youtube_music_logo',
                'isConnected' => boolval(session('ytMusicAccessToken')),
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

        $buttonConfig = array_values(array_filter([
            [
                'providerName' => 'spotify',
                'logo' => Storage::url('spotify_logo.png'),
                'alt' => 'spotify_logo',
                'isConnected' => boolval(session('spotifyAccessToken')),
                'href' => session('spotifyAccessToken') ? 'transfer.target' : 'spotify.authorize',
            ],
            [
                'providerName' => 'ytmusic',
                'logo' => Storage::url('youtube_music_logo.png'),
                'alt' => 'youtube_music_logo',
                'isConnected' => boolval(session('ytMusicAccessToken')),
                'href' => session('ytMusicRefreshToken') ? 'transfer.target' : 'youtube.authorize',
            ],
        ], fn ($config) => $config['providerName'] != $targetProvider));
        
        $header = 'Where would you like to transfer to?';

        return inertia('Transfer/Show',
            [
                'buttonConfig' => $buttonConfig,
                'header' => $header
            ]);
    }
}
