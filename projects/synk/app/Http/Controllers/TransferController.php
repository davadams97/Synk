<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Response;
use Inertia\ResponseFactory;
use Illuminate\Http\RedirectResponse;

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

    public function progress(Request $request): Response
    {
        $selectedTracks = $request->get('selectedTracks', []);
        $source = $request->get('source');
        $target = $request->get('target');

        // Convert array back to Set for the frontend
        $selectedTracksSet = new \Illuminate\Support\Collection($selectedTracks);

        // Perform the actual transfer and get results
        $transferResults = $this->performTransfer($selectedTracks, $source, $target);

        return inertia('Transfer/Progress', [
            'selectedTracks' => $selectedTracksSet,
            'source' => $source,
            'target' => $target,
            'failedTracks' => $transferResults['failed_tracks'] ?? [],
        ]);
    }

    private function performTransfer(array $tracks, string $source, string $target): array
    {
        // Create playlist title
        $playlistTitle = 'Synk generated playlist - ' . now()->format('Y-m-d H:i:s');

        $successfulTracks = [];
        $failedTracks = [];
        $errorMessage = null;

        try {
            // Log the transfer attempt
            Log::info('Starting transfer', [
                'source' => $source,
                'target' => $target,
                'tracks_count' => count($tracks),
                'tracks' => $tracks
            ]);

            // Check if we have valid tokens
            if ($target === 'spotify' && !session('spotifyAccessToken')) {
                throw new \Exception('Spotify access token not found');
            }
            
            if ($target === 'ytMusic' && !session('ytMusicAccessToken')) {
                throw new \Exception('YouTube Music access token not found');
            }

            // Determine the transfer strategy based on target
            $strategy = match ($target) {
                'spotify' => new \App\Services\Strategies\TransferToSpotify(),
                'ytMusic' => new \App\Services\Strategies\TransferToYTMusic(),
                default => throw new \InvalidArgumentException("Unsupported target: {$target}")
            };

            // Set up the appropriate service
            if ($target === 'spotify') {
                $service = new \App\Services\SpotifyService();
            } else {
                $service = new \App\Services\YoutubeMusicService();
            }

            $strategy->setService($service);

            // Perform the actual transfer using the strategy
            try {
                Log::info('Calling transfer strategy', ['target' => $target, 'playlist_title' => $playlistTitle]);
                $strategy->transferPlaylist($tracks, $playlistTitle);
                
                // If we get here, the transfer was successful
                foreach ($tracks as $track) {
                    $successfulTracks[] = [
                        'name' => $track['name'] ?? 'Unknown Track',
                        'artist' => $track['artist'] ?? 'Unknown Artist',
                        'album_art' => $track['albumArt'] ?? 'https://via.placeholder.com/150',
                    ];
                }
                
                Log::info('Transfer completed successfully', [
                    'target' => $target,
                    'successful_tracks' => count($successfulTracks)
                ]);
                
            } catch (\Exception $e) {
                Log::error('Transfer strategy failed', [
                    'target' => $target,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // If the strategy transfer fails, mark all tracks as failed
                foreach ($tracks as $track) {
                    $failedTracks[] = [
                        'name' => $track['name'] ?? 'Unknown Track',
                        'artist' => $track['artist'] ?? 'Unknown Artist',
                        'album_art' => $track['albumArt'] ?? 'https://via.placeholder.com/150',
                        'error_reason' => $e->getMessage(),
                    ];
                }
                $errorMessage = $e->getMessage();
            }

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error('Transfer failed', [
                'source' => $source,
                'target' => $target,
                'tracks' => $tracks,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        // Determine status
        $status = 'completed';
        if (count($failedTracks) > 0 && count($successfulTracks) > 0) {
            $status = 'partial';
        } elseif (count($failedTracks) > 0) {
            $status = 'failed';
        }

        Log::info('Transfer completed', [
            'status' => $status,
            'successful' => count($successfulTracks),
            'failed' => count($failedTracks)
        ]);

        return [
            'successful_tracks' => $successfulTracks,
            'failed_tracks' => $failedTracks,
            'status' => $status,
        ];
    }
}
