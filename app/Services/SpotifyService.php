<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SpotifyService
{
    public function getProfile()
    {
        return Http::spotify()->get('/me')->json();
    }

    public function getPlaylists()
    {
        return Http::spotify()->get('/me/playlists')['items'];
    }

    public function getPlaylist($playlistId)
    {
        return Http::spotify()->get('/playlists/' . $playlistId)->json();
    }

    public function getPlaylistTracks($playlistId)
    {
        $queryParams = ['fields' => 'items(track(name,id,album(name)))'];
        return Http::spotify()->get('/playlists/' . $playlistId . '/tracks', $queryParams)['items'];
    }
}
