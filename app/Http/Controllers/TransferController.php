<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TransferController extends Controller
{
    public function store(Request $request)
    {
        $playlistId = $this->createPlaylist($request['title']);
        $result = $this->findSongFromYTMusic($request['name']);
        $this->addSongToPlaylist($result['videoId'], $playlistId);

        // TODO: check for explicit
        // TODO: check for casing 
        // TODO: check for song name and artist
    }

    private function createPlaylist($title)
    {
        return Http::withHeaders(['Content-Type' => 'application/json'])->post(env('YOUTUBE_API').'playlists', [
            'title' => $title,
        ]);

        // Handle error
    }

    private function findSongFromYTMusic($songName)
    {
        $searchResults = Http::get(env('YOUTUBE_API').'search', [
            'query' => $songName,
        ])->json();

        $matchedResult = array_filter(
            $searchResults,
            fn ($result) => strtolower($result['title']) === strtolower($songName)
        )[0];

        return $matchedResult;
        // Handle error
    }

    private function addSongToPlaylist($videoId, $playlistId)
    {
        Http::withHeaders(['Content-Type' => 'application/json'])->post(env('YOUTUBE_API').'playlists/'.$playlistId.'/add-songs', [
            'videoIds' => [$videoId],
        ]);
    }
}
