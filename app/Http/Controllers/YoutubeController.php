<?php

namespace App\Http\Controllers;

use App\Services\YoutubeMusicService;
use Inertia\Response;

class YoutubeController extends Controller
{
    public function __construct(protected YoutubeMusicService $youtubeMusicService)
    {
    }

    public function index(): Response
    {
        $playlists = $this->youtubeMusicService->getPlaylists();
        $userName = $this->youtubeMusicService->getProfile();

        return inertia('Youtube/Index', [
            'userName' => $userName['name'],
            'playlists' => $playlists
        ]);
    }

    public function show($playlistId): Response
    {
        $playlist = $this->youtubeMusicService->getPlaylist($playlistId);
        return inertia('Youtube/Show', [
            'playlist' => $playlist
        ]);
    }
}
