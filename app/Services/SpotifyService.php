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
        return Http::spotify()->get('/playlists/'.$playlistId)->json();
    }

    public function getPlaylistTracks($playlistId)
    {
        $queryParams = ['fields' => 'items(track(name,id,album(name)))'];

        return Http::spotify()->get('/playlists/'.$playlistId.'/tracks', $queryParams)['items'];
    }

    public function createPlaylist($name, $userId)
    {
        return Http::spotify()->post(
            '/users/'.$userId.'/playlists',
            [
                'name' => $name,
            ]
        );
    }

    public function addToPlaylist($playlistId, $trackIds)
    {
        return Http::spotify()->post(
            '/playlists/'.$playlistId.'/tracks',
            ['uris' => $trackIds, 'position' => 0]
        );
    }

    public function searchTracks($query)
    {
        return Http::spotify()->get(
            '/search',
            [
                'q' => $query,
                'type' => 'track',
            ]
        )['tracks']['items'];
    }

    public function unfollowPlaylist($playlistId)
    {
        return Http::spotify()->delete('/playlist/'.$playlistId.'/followers');
    }
}
