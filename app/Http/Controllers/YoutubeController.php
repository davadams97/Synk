<?php

namespace App\Http\Controllers;

use MGKProd\YTMusic\Facades\YTMusic;
use Inertia\Response;

class YoutubeController extends Controller
{

    public function index(): Response
    {
        $user = YTMusic::browse()->user(env("YOUTUBE_USER_ID"));

        $playlists = YTMusic::browse()->userPlaylists(
            env("YOUTUBE_USER_ID"),
            $user['playlists']['params']
        );

        return inertia('Youtube/Index', [
            'userName' => 'David',
            'playlists' => $user
        ]);
    }
}
