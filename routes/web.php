<?php

use App\Http\Controllers\HomePageController;
use App\Http\Controllers\SpotifyController;
use App\Http\Controllers\YoutubeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

require __DIR__.'/auth.php';

/** Inertia Routes */
Route::get('/', [HomePageController::class, 'index']);

Route::name('spotify')->group(function () {
    Route::name('.playlist')->get('/spotify/playlist', [SpotifyController::class, 'index']);
    Route::name('.playlist.list')->get('/spotify/playlist/{playlistId}', [SpotifyController::class, 'show']);
    Route::name('.playlist.transfer')->post('/spotify/playlist/{playlistId}/transfer', [SpotifyController::class, 'store']);
    Route::name('.authorize')->get('/spotify/auth/redirect', function (Request $request) {
        $request->session()->put('state', $state = Str::random(40));

        $request->session()->put(
            'code_verifier',
            $code_verifier = Str::random(128)
        );

        $codeChallenge = strtr(rtrim(
            base64_encode(hash('sha256', $code_verifier, true)),
            '='
        ), '+/', '-_');

        $query = http_build_query([
            'client_id' => env('SPOTIFY_CLIENT_ID'),
            'redirect_uri' => 'http://localhost:8000/spotify/auth/access-token',
            'response_type' => 'code',
            'scope' => 'user-read-private user-read-email playlist-read-private playlist-modify-public playlist-modify-private',
            'state' => $state,
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
        ]);

        return redirect('https://accounts.spotify.com/authorize?'.$query);
    });
    Route::name('.accessToken')->get('/spotify/auth/access-token', function (Request $request) {
        $state = $request->session()->pull('state');
        $codeVerifier = $request->session()->pull('code_verifier');

        throw_unless(
            strlen($state) > 0 && $state === $request->state,
            InvalidArgumentException::class
        );

        $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
            'grant_type' => 'authorization_code',
            'client_id' => env('SPOTIFY_CLIENT_ID'),
            'redirect_uri' => 'http://localhost:8000/spotify/auth/access-token',
            'code_verifier' => $codeVerifier,
            'code' => $request->code,
        ]);

        session(
            [
                'spotifyAccessToken' => $response->json('access_token'),
                'spotifyRefreshToken' => $response->json('refresh_token'),
                'expiresAt' => now()->addSeconds($response->json('expires_in'))->timestamp,
            ]
        );

        return redirect()->route('spotify.playlist');
    });
});

Route::name('youtube')->group(function () {
    Route::name('.playlist')->get('/youtube/playlist', [YoutubeController::class, 'index']);
    Route::name('.playlist.list')->get('/youtube/playlist/{playlistId}', [YoutubeController::class, 'show']);
    Route::name('.playlist.transfer')->post('/youtube/playlist/{playlistId}/transfer', [YoutubeController::class, 'store']);
});
