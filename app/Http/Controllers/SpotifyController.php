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
        $userName = $this->spotifyService->getProfile()['display_name'];
        $playlists = array_map(
            fn ($playlist) => [
                'columns' => [$playlist['name']],
                'id' => $playlist['id'],
            ],
            $this->spotifyService->getPlaylists()
        );

        return inertia('Provider/Index', [
            'userName' => $userName,
            'playlists' => $playlists,
        ]);
    }
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
