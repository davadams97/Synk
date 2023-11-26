<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Inertia\Response;

class YoutubeController extends Controller
{

    public function index(): Response
    {
        $playlists = Http::get(env('YOUTUBE_API') . 'library/playlists')->json();
        $userName = Http::get(env('YOUTUBE_API') . 'browse/user/' . env('YOUTUBE_CHANNEL_ID'))->json();
        return inertia('Youtube/Index', [
            'userName' => $userName['name'],
            'playlists' => $playlists
        ]);
    
    }
}
