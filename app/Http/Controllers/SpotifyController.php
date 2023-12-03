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
        $userName = $this->spotifyService->getProfile()["display_name"];
        $playlists = array_map(fn ($playlist) => array('columns' => array($playlist['name']), 'id' => $playlist['id']), $this->spotifyService->getPlaylists());

        return inertia('Spotify/Index', [
            'userName' => $userName,
            'playlists' => $playlists
        ]);
    }

    public function show($playlistId): Response
    {
        $playlist = array_map(fn ($entry) => array('name' => $entry['track']['name'], 'album' => $entry['track']['album']['name']), $this->spotifyService->getPlaylist($playlistId));
        return inertia('Spotify/Show', [
            'playlist' => $playlist
        ]);
    }
}
