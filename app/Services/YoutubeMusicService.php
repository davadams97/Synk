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
}
