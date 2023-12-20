<?php

use App\Http\Controllers\HomePageController;
use App\Http\Controllers\SpotifyController;
use App\Http\Controllers\YoutubeController;
use Google\Client;
use Google\Service\YouTube;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

require __DIR__.'/auth.php';

/** Inertia Routes */
Route::get('/', [HomePageController::class, 'index']);

Route::get('/home', [HomePageController::class, 'show']);

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

        session()->forget(['state','code_verifier']);

        session(
            [
                'spotifyAccessToken' => $response->json('access_token'),
                'spotifyRefreshToken' => $response->json('refresh_token'),
                'spotifyExpiresAt' => now()->addSeconds($response->json('expires_in'))->timestamp,
            ]
        );

        return redirect()->route('spotify.playlist');
    });
});

Route::name('youtube')->group(function () {
    Route::name('.playlist')->get('/youtube/playlist', [YoutubeController::class, 'index']);
    Route::name('.playlist.list')->get('/youtube/playlist/{playlistId}', [YoutubeController::class, 'show']);
    Route::name('.playlist.transfer')->post('/youtube/playlist/{playlistId}/transfer', [YoutubeController::class, 'store']);
    Route::name('.authorize')->get('/youtube/auth/redirect', function () {
        $client = new Client();
        $client->setAuthConfig(json_decode(env('GOOGLE_CLIENT_SECRET'), true));
        $client->setRedirectUri('http://localhost:8000/youtube/auth/access-token');
        $client->addScope(YOUTUBE::class::YOUTUBE_FORCE_SSL);
        $client->setAccessType('offline');
        $client->setState(Str::random(40));
        $client->setPrompt('consent');
        $auth_url = $client->createAuthUrl();

        return redirect($auth_url);
    });
    Route::name('.accessToken')->get('/youtube/auth/access-token', function (Request $request) {
        $client = new Client();
        $client->setAuthConfig(json_decode(env('GOOGLE_CLIENT_SECRET'), true));
        $client->addScope(YOUTUBE::class::YOUTUBE_FORCE_SSL);

        $client->fetchAccessTokenWithAuthCode($request['code']);

        session(
            [
                'ytMusicAccessToken' => $client->getAccessToken()['access_token'],
                'ytMusicRefreshToken' => $client->getRefreshToken(),
                'ytMusicExpiresAt' => now()->addSeconds($client->getAccessToken()['expires_in'])->timestamp,
            ]
        );

        return redirect()->route('youtube.playlist');
    });
});
