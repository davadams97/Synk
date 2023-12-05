<?php

namespace App\Http\Controllers;

use App\Services\SpotifyService;
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

        return inertia('Spotify/Index', [
            'userName' => $userName,
            'playlists' => $playlists,
        ]);
    }

    public function show($playlistId): Response
    {
        $playlistName = $this->spotifyService->getPlaylist($playlistId)['name'];
        $playlistTracks = $this->spotifyService->getPlaylistTracks($playlistId);
        $playlist = array_map(
            fn ($entry) => [
                'columns' => [$entry['track']['name'], $entry['track']['album']['name']],
                'id' => $entry['track']['id'],
            ],
            $playlistTracks
        );

        return inertia('Spotify/Show', [
            'playlist' => $playlist,
            'playlistName' => $playlistName,
        ]);
    }
}
