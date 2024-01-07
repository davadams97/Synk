<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Response;

class TransferController extends Controller
{
    public function source(): Response
    {
        // Store the last route and query params since Spotify and YouTube authorization happens outside app domain
        session(['lastRoute' => 'transfer.source']);

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
                'href' => session('ytMusicRefreshToken') ? 'transfer.target' : 'ytMusic.authorize',
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
        $targetProvider = $request['source'];
        // Store the last route and query params since Spotify and YouTube authorization happens outside app domain
        session(['lastRoute' => 'transfer.target', 'queryParams' => 'source='.$sourceProvider]);

        $buttonConfig = [
            [
                'providerName' => 'spotify',
                'logo' => Storage::url('spotify_logo.png'),
                'alt' => 'spotify_logo',
                'isConnected' => boolval(session('spotifyAccessToken')),
                'href' => session('spotifyAccessToken') ? 'spotify.playlist' : 'spotify.authorize',
            ],
            [
                'providerName' => 'ytmusic',
                'logo' => Storage::url('youtube_music_logo.png'),
                'alt' => 'youtube_music_logo',
                'isConnected' => boolval(session('ytMusicAccessToken')),
                'href' => session('ytMusicRefreshToken') ? 'ytMusic.playlist' : 'ytMusic.authorize',
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
