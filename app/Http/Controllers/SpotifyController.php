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

        return inertia('Playlist/Index', [
            'userName' => $userName,
            'playlists' => $playlists,
        ]);
    }

    public function show($playlistId): Response
    {
        $playlistName = $this->spotifyService->getPlaylist($playlistId)['name'];
        $playlistTracks = $this->spotifyService->getPlaylistTracks($playlistId);
        $trackList = array_map(
            fn ($entry) => [
                'columns' => [$entry['track']['name'], $entry['track']['album']['name']],
                'id' => $entry['track']['id'],
            ],
            $playlistTracks
        );

        return inertia('Spotify/Show', [
            'trackList' => $trackList,
            'playlistName' => $playlistName,
            'playlistId' => $playlistId,
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
