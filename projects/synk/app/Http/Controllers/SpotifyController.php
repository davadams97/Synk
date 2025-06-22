<?php

namespace App\Http\Controllers;

use App\Services\Factories\TransferStrategyFactory;
use App\Services\SpotifyService;
use App\Services\YoutubeMusicService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
                'isSelected' => False,
                'href' => $playlist['uri'],
                'coverURL' => $playlist['images'] && count($playlist['images']) ? $playlist['images'][0]['url'] : Storage::url('no_art.png'),
                'trackCount' => $playlist['tracks']['total'] ?? 0,
                'tracks' => [], // Empty initially, will be loaded on demand
            ],
            $this->spotifyService->getPlaylists()
        );

        $playlistLength = count($playlists);

        return inertia('Provider/Index', [
            'playlists' => $playlists,
            'header' => "Playlists ({$playlistLength})",
            'transferRoute' => "spotify.playlist.transfer"
        ]);
    }

    public function getPlaylistTracks(Request $request, $playlistId)
    {
        $tracks = array_map(
            fn ($entry) => [
                'id' => $entry['track']['id'] ?? null,
                'name' => $entry['track']['name'] ?? 'Unknown Track',
                'href' => $entry['track']['uri'] ?? '#',
                'albumName' => $entry['track']['album']['name'] ?? 'Unknown Album',
                'albumArt' => $entry['track']['album']['images'][0]['url'] ?? Storage::url('no_art.png'),
            ],
            $this->spotifyService->getPlaylistTracks($playlistId)
        );

        return response()->json(['tracks' => $tracks]);
    }

    public function store(Request $request)
    {
        $strategy = $request['targetProvider'];

        // TODO: handle case when target provider is not provided

        $transferStrategy = TransferStrategyFactory::create($strategy);

        if ($strategy == 'ytMusic') {
            $transferStrategy->setService(new YoutubeMusicService());
        }

        $transferStrategy->transferPlaylist($request['name'], $request['title']);
    }
}
