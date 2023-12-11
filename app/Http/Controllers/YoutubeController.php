<?php

namespace App\Http\Controllers;

use App\Services\SpotifyService;
use App\Services\YoutubeMusicService;
use Illuminate\Http\Request;
use Inertia\Response;
use TransferStrategyFactory;

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

        return inertia('Youtube/Index', [
            'userName' => $userName['name'],
            'playlists' => $playlists,
        ]);
    }

    public function show($playlistId): Response
    {
        $playlistData = $this->youtubeMusicService->getPlaylist($playlistId);
        $playlistName = $playlistData['title'];

        $playlist = array_map(
            fn ($entry) => [
                'columns' => [$entry['title'], $entry['album']['name'] ?? ''],
                'id' => $entry['videoId'],
            ],
            $playlistData['tracks']
        );

        return inertia('Youtube/Show', [
            'playlist' => $playlist,
            'playlistName' => $playlistName,
        ]);
    }

    public function store(Request $request, $playlistId): Response
    {
        $strategy = $request['targetProvider'];
        $transferStrategy = TransferStrategyFactory::create($strategy);

        if ($strategy == 'spotify') {
            $transferStrategy->setService(new SpotifyService());
        }

        $transferStrategy->transferPlaylist($request['name'], $playlistId);
    }
}
