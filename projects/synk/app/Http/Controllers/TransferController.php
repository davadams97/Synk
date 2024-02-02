<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Response;
use Inertia\ResponseFactory;

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
                'providerName' => 'ytMusic',
                'logo' => Storage::url('youtube_music_logo.png'),
                'alt' => 'youtube_music_logo',
                'isConnected' => boolval(session('ytMusicAccessToken')),
                'href' => session('ytMusicRefreshToken') ? 'transfer.target' : 'ytMusic.authorize',
            ],
        ];

        $header = 'Where would you like to transfer from?';

        return inertia('Transfer/Source',
            [
                'buttonConfig' => $buttonConfig,
                'header' => $header,
            ]);
    }

    public function target(Request $request): Response|ResponseFactory|RedirectResponse
    {
        $sourceProvider = $request['source'];

        if (!$sourceProvider) {
            return redirect()->route('transfer.source');
        }

        // Store the last route and query params since Spotify and YouTube authorization happens outside app domain
        session(['lastRoute' => 'transfer.target', 'queryParams' => 'source='.$sourceProvider]);

        $buttonConfig = [
            [
                'providerName' => 'spotify',
                'logo' => Storage::url('spotify_logo.png'),
                'alt' => 'spotify_logo',
                'isConnected' => boolval(session('spotifyAccessToken')),
                'href' => session('spotifyAccessToken') ? $sourceProvider . '.playlist' : 'spotify.authorize',
            ],
            [
                'providerName' => 'ytMusic',
                'logo' => Storage::url('youtube_music_logo.png'),
                'alt' => 'youtube_music_logo',
                'isConnected' => boolval(session('ytMusicAccessToken')),
                'href' => session('ytMusicRefreshToken') ? $sourceProvider . '.playlist' : 'ytMusic.authorize',
            ],
        ];

        $header = 'Where would you like to transfer to?';

        return inertia('Transfer/Target',
            [
                'buttonConfig' => $buttonConfig,
                'header' => $header,
                'sourceProvider' => $sourceProvider
            ]);
    }
}
