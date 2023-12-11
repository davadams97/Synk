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
        $songsToAdd = [];

        if ($request['currentProvider'] == 'spotify') {
            if ($request['targetProvider'] == 'ytmusic') {
                foreach ($request['name'] as $song) {
                    $result = $this->findSongFromYTMusic($song);

                    if (! empty($result)) {
                        $songsToAdd[] = $result[0]['item'];
                    }
                }

                $response = $this->createPlaylist('ytmusic', $request['title']);

                if ($response->successful()) {
                    $songsToAddIds = array_map(fn ($song) => $song['videoId'], $songsToAdd);
                    $this->addSongToPlaylist('ytmusic', $songsToAddIds, $response);
                }

                if ($response->failed()) {
                    $this->deletePlaylist($response);
                }
            }
        }

        if ($request['currentProvider'] == 'ytmusic') {
            if ($request['targetProvider'] == 'spotify') {
                foreach ($request['name'] as $song) {
                    $result = $this->findSongFromSpotify($song);
                    if (! empty($result)) {
                        $songsToAdd[] = $result[0]['item'];
                    }
                }

                // info(array_map(fn ($song) => $song['uri'], $songsToAdd));

                // info($songsToAdd);
                $userProfile = $this->spotifyService->getProfile();
                $playlists = $this->spotifyService->getPlaylists();

                $filter = [
                    'keys' => [
                        'name',
                    ],
                    'shouldSort' => true,
                ];
                $fuse = new Fuse($playlists, $filter);

                // $matchedResult = $fuse->search(['title' => $songName], ['limit' => 1]);
                $matchedResult = $fuse->search($request['title'], ['limit' => 1]);

                $response = $this->spotifyService->createPlaylist($request['title'], $userProfile['id']);

                if ($response->successful()) {
                    $songsToAddIds = array_map(fn ($song) => $song['uri'], $songsToAdd);
                    info($matchedResult);
                    $this->addSongToPlaylist('spotify', $songsToAddIds, $matchedResult[0]['item']['id']);
                }

                // if ($response->failed()) {
                //     $this->deletePlaylist($response);
                // }
            }
        }

        // TODO: check for explicit
        // TODO: check for song name and artist
    }

    private function createPlaylist(string $provider, string $title)
    {
        switch ($provider) {
            case 'ytmusic':
                $response = Http::withHeaders(['Content-Type' => 'application/json'])->post(env('YOUTUBE_API').'playlists', [
                    'title' => $title,
                ]);

                $response->throw();

                return $response;

            case 'spotify':
                $userId = $this->spotifyService->getProfile()['id'];

                $response = $this->spotifyService->createPlaylist($userId, $title);

                $response->throw();

                return;
        }
    }

    private function deletePlaylist(string $playlistId)
    {
        $response = Http::delete(env('YOUTUBE_API').'playlists/'.$playlistId);
        $response->throw();

        return $response;
    }

    private function findSongFromYTMusic(string $songName)
    {
        // TODO: look into getter function not working
        $filter = [
            'keys' => [
                // ['name' => 'title', 'getFn' => fn ($query) => $query['title']],
                // ['name' => 'artistName', 'getFn' => fn ($query) => $query['artists']['name']],
                // ['name' => 'albumName', 'getFn' => fn ($query) => $query['album']['name']],
                'title',
                'artists.name',
                'album.name',
            ],
            'shouldSort' => true,
        ];

        $response = Http::get(env('YOUTUBE_API').'search', [
            'query' => $songName,
        ]);

        $response->throw();

        $fuse = new Fuse($response->json(), $filter);
        // $matchedResult = $fuse->search(['title' => $songName], ['limit' => 1]);
        $matchedResult = $fuse->search($songName, ['limit' => 1]);

        return $matchedResult;
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
            case 'ytmusic':
                $response = Http::withHeaders(['Content-Type' => 'application/json'])->post(env('YOUTUBE_API').'playlists/'.$playlistId.'/add-songs', [
                    'videoIds' => $trackIds,
                ]);
                $response->throw();

                break;

            case 'spotify':
                $response = $this->spotifyService->addToPlaylist($playlistId, $trackIds);
                $response->throw();

                break;
        }
    }
}
