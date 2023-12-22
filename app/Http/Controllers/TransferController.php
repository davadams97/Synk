<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Inertia\Response;

class TransferController extends Controller
{
    public function source(): Response
    {
        $buttonConfig = [
            [
                'providerName' => 'Spotify',
                'logo' => Storage::url('spotify_logo.png'),
                'alt' => 'spotify_logo',
                'isConnected' => session('spotifyAccessToken'),
                'href' => session('spotifyAccessToken') ? route('spotify.playlist') : route('spotify.authorize'),
            ],
            [
                'providerName' => 'Youtube Music',
                'logo' => Storage::url('youtube_music_logo.png'),
                'alt' => 'youtube_music_logo',
                'isConnected' => session('ytMusicAccessToken'),
                'href' => session('ytMusicRefreshToken') ? route('youtube.playlist') : route('youtube.authorize'),
            ],
        ];

        $header = 'Where would you like to transfer from?';

        return inertia('Transfer/Show',
            [
                'buttonConfig' => $buttonConfig,
                'header' => $header
            ]);
    }
}
