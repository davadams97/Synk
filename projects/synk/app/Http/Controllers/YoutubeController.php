<?php

namespace App\Http\Controllers;

use App\Services\Factories\TransferStrategyFactory;
use App\Services\SpotifyService;
use App\Services\YoutubeMusicService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
                'id' => $playlist['playlistId'],
                'name' => $playlist['title'],
                'isSelected' => False,
                'coverURL' => count($playlist['thumbnails']) ? $playlist['thumbnails'][0]['url'] : Storage::url('no_art.png'),
                'trackCount' => $playlist['count'] ?? 0,
                'tracks' => [], // Empty initially, will be loaded on demand
            ],
            $this->youtubeMusicService->getPlaylists()
        );

        $playlistLength = count($playlists);

        return inertia('Provider/Index', [
            'playlists' => $playlists,
            'header' => "Playlists ({$playlistLength})",
            'transferRoute' => "ytMusic.playlist.transfer"
        ]);
    }

    public function getPlaylistTracks(Request $request, $playlistId)
    {
        $playlist = $this->youtubeMusicService->getPlaylist($playlistId);
        
        $tracks = array_map(
            fn ($entry) => [
                'id' => $entry['videoId'] ?? null,
                'name' => $entry['title'] ?? 'Unknown Track',
                'artist' => $entry['artists'][0]['name'] ?? 'Unknown Artist',
                'albumName' => $entry['album']['name'] ?? 'Unknown Album',
                'albumArt' => count($entry['thumbnails']) ? $entry['thumbnails'][0]['url'] : Storage::url('no_art.png')
            ],
            $playlist['tracks']
        );

        return response()->json(['tracks' => $tracks]);
    }

    public function store(Request $request, $playlistId)
    {
        $strategy = $request['targetProvider'];

        // TODO: handle case when target provider is not provided

        $transferStrategy = TransferStrategyFactory::create($strategy);

        if ($strategy == 'spotify') {
            $transferStrategy->setService(new SpotifyService());
        }

        $transferStrategy->transferPlaylist($request['name'], $playlistId, $request['title']);
    }
}
