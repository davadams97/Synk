<?php

namespace App\Http\Controllers;

use App\Services\Factories\TransferStrategyFactory;
use App\Services\SpotifyService;
use App\Services\YoutubeMusicService;
use Illuminate\Http\Request;
use Inertia\Response;

class YoutubeController extends Controller
{
    public function __construct(protected YoutubeMusicService $youtubeMusicService)
    {
    }

    public function index(): Response
    {
        $playlists = array_map(
            fn ($playlist) => [
                'columns' => [$playlist['title']],
                'id' => $playlist['playlistId'],
            ],
            $this->youtubeMusicService->getPlaylists()
        );
        $userName = $this->youtubeMusicService->getProfile();

        return inertia('Provider/Index', [
            'userName' => $userName['name'],
            'playlists' => $playlists,
        ]);
            'transferRoute' => "ytMusic.playlist.transfer"
            'playlistId' => $playlistId,
        ]);
    }

    public function store(Request $request, $playlistId)
    {
        $strategy = $request['targetProvider'];
        $transferStrategy = TransferStrategyFactory::create($strategy);

        if ($strategy == 'spotify') {
            $transferStrategy->setService(new SpotifyService());
        }

        $transferStrategy->transferPlaylist($request['name'], $playlistId, $request['title']);
    }
}
