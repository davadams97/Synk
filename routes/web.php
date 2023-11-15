<?php

use App\Http\Controllers\HomePageController;
use App\Http\Controllers\SpotifyController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

require __DIR__ . '/auth.php';

/** Inertia Routes */
Route::get('/', [HomePageController::class, 'index']);

Route::name('spotify')->group(function() {
    Route::name('.playlist')->get('/playlist', [SpotifyController::class, 'index']);
Route::name('youtube')->group(function () {
    Route::name('.playlist')->get('/youtube/playlist', [YoutubeController::class, 'index']);
});

/** Spotify API Routes */
Route::get('auth/redirect', function (Request $request) {
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
        'redirect_uri' => 'http://localhost:8000/auth/access-token',
        'response_type' => 'code',
        'scope' => 'user-read-private user-read-email playlist-read-private',
        'state' => $state,
        'code_challenge' => $codeChallenge,
        'code_challenge_method' => 'S256',
    ]);

    return redirect('https://accounts.spotify.com/authorize?' . $query);
})->name('spotify.authorize');

Route::get('auth/access-token', function (Request $request) {
    $state = $request->session()->pull('state');
    $codeVerifier = $request->session()->pull('code_verifier');

    throw_unless(
        strlen($state) > 0 && $state === $request->state,
        InvalidArgumentException::class
    );

    $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
        'grant_type' => 'authorization_code',
        'client_id' => env('SPOTIFY_CLIENT_ID'),
        'redirect_uri' => 'http://localhost:8000/auth/access-token',
        'code_verifier' => $codeVerifier,
        'code' => $request->code,
    ]);

    session(['spotifyAccessToken' => $response->json('access_token')]);
    session(['spotifyRefreshToken' => $response->json('refresh_token')]);

    return redirect()->route('spotify.playlist');
})->name('spotify.accessToken');
