<?php

namespace App\Http\Controllers;

use App\Services\Factories\TransferStrategyFactory;
use App\Services\SpotifyService;
use App\Services\YoutubeMusicService;
use Illuminate\Http\Request;
use Inertia\Response;

class SpotifyController extends Controller
{
    public function __construct(protected SpotifyService $spotifyService)
    {
    }

    public function index(): Response
    {
        $playlists = array_map(
            fn ($playlist) => [
                'id' => $playlist['id'],
                'name' => $playlist['name'],
                'tracks' => array_map(
                    fn ($entry) => [
                        'id' => $entry['track']['id'],
                        'name' => $entry['track']['name'],
                        'albumName' => $entry['track']['album']['name'],
                    ],
                    $this->spotifyService->getPlaylistTracks($playlist['id'])),

            ],
            $this->spotifyService->getPlaylists()
        );

        $playlistLength = count($playlists);

        return inertia('Provider/Index', [
            'playlists' => $playlists,
            'header' => "Playlists ({$playlistLength})"
        ]);
    }

    public function store(Request $request, $playlistId)
    {
        $strategy = $request['targetProvider'];
        $transferStrategy = TransferStrategyFactory::create($strategy);

        if ($strategy == 'ytmusic') {
            $transferStrategy->setService(new YoutubeMusicService());
        }

        $transferStrategy->transferPlaylist($request['name'], $playlistId, $request['title']);
    }
}
