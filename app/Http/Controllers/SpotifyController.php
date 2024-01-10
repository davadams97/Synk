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
                'coverURL' => count($playlist['images']) ? $playlist['images'][0]['url'] : Storage::url('no_art.png'),
                'tracks' => array_map(
                    fn ($entry) => [
                        'id' => $entry['track']['id'],
                        'name' => $entry['track']['name'],
                        'albumName' => $entry['track']['album']['name'],
                        'albumArt' => $entry['track']['album']['images'][0]['url']
                    ],
                    $this->spotifyService->getPlaylistTracks($playlist['id'])),
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
