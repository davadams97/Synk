<?php

namespace App\Http\Controllers;

use Inertia\Response;

class SpotifyController extends Controller
{
    public function index(): Response
    {
        return inertia('Spotify/Index');
    }
}
