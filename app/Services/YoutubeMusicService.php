<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class YoutubeMusicService
{
    public function getProfile()
    {
        return Http::get(env('YOUTUBE_API').'users/'.env('YOUTUBE_CHANNEL_ID'))->json();
    }

    public function getPlaylists()
    {
        return Http::get(env('YOUTUBE_API').'playlists')->json();
    }

    public function getPlaylist($playlistId)
    {
        return Http::get(env('YOUTUBE_API').'playlists/'.$playlistId)->json();
    }

    public function createPlaylist(string $title)
    {
        return Http::withHeaders(['Content-Type' => 'application/json'])->post(env('YOUTUBE_API').'playlists', [
            'title' => $title,
        ]);
    }

    public function deletePlaylist(string $playlistId)
    {
        return Http::delete(env('YOUTUBE_API').'playlists/'.$playlistId);
    }

    public function addToPlaylist(string $playlistId, $trackIds)
    {
        return Http::withHeaders(['Content-Type' => 'application/json'])->post(env('YOUTUBE_API').'playlists/'.$playlistId.'/add-songs', [
            'videoIds' => $trackIds,
        ]);
    }

    public function searchTracks($query)
    {
        return Http::get(env('YOUTUBE_API').'search', [
            'query' => $query,
        ]);
    }
}
