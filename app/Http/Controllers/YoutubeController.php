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
                'coverURL' => count($playlist['thumbnails']) ? $playlist['thumbnails'][0]['url'] : Storage::url('no_art.png'),
                'tracks' => array_map(
                    fn ($entry) => [
                        'id' => $entry['videoId'],
                        'name' => $entry['title'],
                        'albumName' => $entry['album']['name'] ?? '',
                        'albumArt' => count($entry['thumbnails']) ? $entry['thumbnails'][0]['url'] : Storage::url('no_art.png')
                    ],
                    $this->youtubeMusicService->getPlaylist($playlist['playlistId'])['tracks']),
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
