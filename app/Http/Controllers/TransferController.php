<?php

namespace App\Http\Controllers;

use App\Services\SpotifyService;
use Fuse\Fuse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TransferController extends Controller
{
    public function __construct(protected SpotifyService $spotifyService)
    {
    }

    public function store(Request $request)
    {
        $strategy = $request['currentPro/vider'];
        // $this->transferStrategy = TransferStrategyFactory::create($strategyType);

        // $songsToAdd = [];

        // if ($request['currentProvider'] == 'spotify') {
        //     if ($request['targetProvider'] == 'ytmusic') {
        //         foreach ($request['name'] as $song) {
        //             $result = $this->findSongFromYTMusic($song);

        //             if (! empty($result)) {
        //                 $songsToAdd[] = $result[0]['item'];
        //             }
        //         }

        //         $response = $this->createPlaylist('ytmusic', $request['title']);

        //         if ($response->successful()) {
        //             $songsToAddIds = array_map(fn ($song) => $song['videoId'], $songsToAdd);
        //             $this->addSongToPlaylist('ytmusic', $songsToAddIds, $response);
        //         }

        //         if ($response->failed()) {
        //             $this->deletePlaylist($response);
        //         }
        //     }
        // }

        // TODO: check for explicit
        // TODO: check for song name and artist
    }

    private function createPlaylist(string $provider, string $title)
    {
        switch ($provider) {
            case 'spotify':
                $userId = $this->spotifyService->getProfile()['id'];

                $response = $this->spotifyService->createPlaylist($userId, $title);

                $response->throw();

                return;
        }
    }
    private function findSongFromSpotify(string $songName)
    {
        // TODO: look into getter function not working
        $filter = [
            'keys' => [
                // ['name' => 'title', 'getFn' => fn ($query) => $query['title']],
                // ['name' => 'artistName', 'getFn' => fn ($query) => $query['artists']['name']],
                // ['name' => 'albumName', 'getFn' => fn ($query) => $query['album']['name']],
                'name',
                'artists.name',
                'album.name',
            ],
            'shouldSort' => true,
        ];

        $response = $this->spotifyService->searchTracks($songName);

        // info($response);

        $fuse = new Fuse($response, $filter);
        // $matchedResult = $fuse->search(['title' => $songName], ['limit' => 1]);
        $matchedResult = $fuse->search($songName, ['limit' => 1]);

        return $matchedResult;
    }

    private function addSongToPlaylist(string $provider, array $trackIds, string $playlistId)
    {
        switch ($provider) {

            case 'spotify':
                $response = $this->spotifyService->addToPlaylist($playlistId, $trackIds);
                $response->throw();

                break;
        }
    }
}
