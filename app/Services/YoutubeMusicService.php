<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class YoutubeMusicService
{
    public function getProfile()
    {
        return Http::ytmusic()->get('/users/' . env('YOUTUBE_CHANNEL_ID'))->json();
    }

    public function getPlaylists()
    {
        return Http::ytmusic()->get('/playlists')->json();
    }

    public function getPlaylist($playlistId)
    {
        return Http::ytmusic()->get('/playlists/' . $playlistId)->json();
    }

    public function createPlaylist(string $title)
    {
        return Http::ytmusic()->withHeaders(['Content-Type' => 'application/json'])->post('/playlists', [
            'title' => $title,
        ]);
    }

    public function deletePlaylist(string $playlistId)
    {
        return Http::ytmusic()->delete('/playlists/' . $playlistId);
    }

    public function addToPlaylist(string $playlistId, $trackIds)
    {
        return Http::ytmusic()->withHeaders(['Content-Type' => 'application/json'])->post('/playlists/' . $playlistId, [
            'videoIds' => $trackIds,
        ]);
    }

    public function searchTracks($query)
    {
        return Http::ytmusic()->get('/search', [
            'query' => $query,
        ]);
    }
}
