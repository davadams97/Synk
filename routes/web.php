<?php

use App\Http\Controllers\HomePageController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

require __DIR__ . '/auth.php';

/** Inertia Routes */
Route::get('/', [HomePageController::class, 'index']);

/** Spotify API Routes */
Route::get('/redirect', function (Request $request) {
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
        'redirect_uri' => 'http://localhost:8000/access-token',
        'response_type' => 'code',
        'scope' => 'user-read-private user-read-email',
        'state' => $state,
        'code_challenge' => $codeChallenge,
        'code_challenge_method' => 'S256',
    ]);

    return redirect('https://accounts.spotify.com/authorize?' . $query);
})->name('spotify.authorize');

Route::get('/access-token', function (Request $request) {
    $state = $request->session()->pull('state');
    $codeVerifier = $request->session()->pull('code_verifier');
 
    throw_unless(
        strlen($state) > 0 && $state === $request->state,
        InvalidArgumentException::class
    );
 
    $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
        'grant_type' => 'authorization_code',
        'client_id' => env('SPOTIFY_CLIENT_ID'),
        'redirect_uri' => 'http://localhost:8000/access-token',
        'code_verifier' => $codeVerifier,
        'code' => $request->code,
    ]);
 
    return $response->json();
})->name('spotify.accessToken');
