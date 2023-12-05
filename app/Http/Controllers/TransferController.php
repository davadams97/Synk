<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TransferController extends Controller
{
    public function store(Request $request)
    {
        Http::withHeaders(['Content-Type' => 'application/json'])->post(env('YOUTUBE_API') . 'playlists', [
            'title' => $request['title']
        ]);
    }
}
