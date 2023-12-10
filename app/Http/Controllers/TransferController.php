<?php

namespace App\Http\Controllers;

use Fuse\Fuse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TransferController extends Controller
{
    public function store(Request $request)
    {
        $songsToAdd = [];

        foreach ($request['name'] as $song) {
            $result = $this->findSongFromYTMusic($song);

            if (!empty($result)) {
                $songsToAdd[] = $result[0]['item'];
            }
        }

        $response = $this->createPlaylist($request['title']);

        if ($response->successful()) {
            $songsToAddIds = array_map(fn ($song) => $song['videoId'], $songsToAdd);
            $this->addSongToPlaylist($songsToAddIds, $response);
        }

        if ($response->failed()) {
            $this->deletePlaylist($response);
        }

        // TODO: check for explicit
        // TODO: check for casing
        // TODO: check for song name and artist
    }

    private function createPlaylist(String $title)
    {
        $response = Http::withHeaders(['Content-Type' => 'application/json'])->post(env('YOUTUBE_API').'playlists', [
            'title' => $title,
        ]);

        $response->throw();

        return $response;
    }

    private function deletePlaylist(String $playlistId)
    {
        $response = Http::delete(env('YOUTUBE_API').'playlists/'.$playlistId);

        $response->throw();

        return $response;
    }

    private function findSongFromYTMusic(String $songName)
    {
        $filter = [
            'keys' =>
            ['title', 'artists.name', 'album.name'],
            'shouldSort' => true,
        ];

        $response = Http::get(env('YOUTUBE_API').'search', [
            'query' => $songName,
        ]);

        $response->throw();

        $fuse = new Fuse($response->json(), $filter);
        $matchedResult = $fuse->search($songName, ['limit' => 1]);

        return $matchedResult;
    }

    private function addSongToPlaylist(array $videoIds, String $playlistId)
    {
        $response = Http::withHeaders(['Content-Type' => 'application/json'])->post(env('YOUTUBE_API').'playlists/'.$playlistId.'/add-songs', [
            'videoIds' => $videoIds,
        ]);

        $response->throw();
    }
}
