<?php

use App\Http\Controllers\HomePageController;
use App\Http\Controllers\SpotifyController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\YoutubeController;
use App\Http\Middleware\EnsureSpotifyTokenIsValid;
use App\Http\Middleware\EnsureYtMusicTokenIsValid;
use Google\Client;
use Google\Service\YouTube;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

require __DIR__.'/auth.php';

/** Inertia Routes */
Route::name('home')->get('/', [HomePageController::class, 'index']);

Route::name('transfer')->group(function () {
    Route::name('.source')->get('/transfer/source', [TransferController::class, 'source']);
    Route::name('.target')->get('/transfer/target', [TransferController::class, 'target']);
    Route::name('.progress')->post('/transfer/progress', [TransferController::class, 'progress']);
});

Route::middleware(EnsureSpotifyTokenIsValid::class)->name('spotify')->group(function () {
    Route::name('.playlist')->get('/spotify/playlist', [SpotifyController::class, 'index']);
    Route::name('.playlist.transfer')->post('/spotify/playlist/transfer', [SpotifyController::class, 'store']);
    Route::name('.playlist.tracks')->get('/spotify/playlist/{playlistId}/tracks', [SpotifyController::class, 'getPlaylistTracks']);
    Route::withoutMiddleware(EnsureSpotifyTokenIsValid::class)->name('.authorize')->get('/spotify/auth/redirect', function (Request $request) {
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
    Route::withoutMiddleware(EnsureSpotifyTokenIsValid::class)->name('.accessToken')->get('/spotify/auth/access-token', function (Request $request) {
        $state = $request->session()->pull('state');
        $codeVerifier = $request->session()->pull('code_verifier');

        // Validate state parameter
        if (empty($state) || $state !== $request->get('state')) {
            return redirect()->route('home')->with('error', 'Invalid state parameter');
        }

        // Validate code parameter
        if (!$request->has('code')) {
            return redirect()->route('home')->with('error', 'Authorization code not received');
        }

        try {
            $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
                'grant_type' => 'authorization_code',
                'client_id' => env('SPOTIFY_CLIENT_ID'),
                'client_secret' => env('SPOTIFY_CLIENT_SECRET'),
                'redirect_uri' => 'http://localhost:8000/spotify/auth/access-token',
                'code_verifier' => $codeVerifier,
                'code' => $request->get('code'),
            ]);

            if (!$response->successful()) {
                return redirect()->route('home')->with('error', 'Failed to exchange authorization code');
            }

            $responseData = $response->json();

            // Clear any existing session data
            session()->forget(['state', 'code_verifier']);

            // Store the tokens in session
            session([
                'spotifyAccessToken' => $responseData['access_token'],
                'spotifyRefreshToken' => $responseData['refresh_token'] ?? null,
                'spotifyExpiresAt' => now()->addSeconds($responseData['expires_in'])->timestamp,
            ]);

            // Redirect back to the original route or home
            $lastRoute = session('lastRoute', 'home');
            $queryParams = session('queryParams', []);
            
            return redirect()->route($lastRoute, $queryParams);

        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Authentication failed: ' . $e->getMessage());
        }
    });
});

Route::middleware(EnsureYtMusicTokenIsValid::class)->name('ytMusic')->group(function () {
    Route::name('.playlist')->get('/youtube/playlist', [YoutubeController::class, 'index']);
    Route::name('.playlist.transfer')->post('/youtube/playlist/transfer', [YoutubeController::class, 'store']);
    Route::name('.playlist.tracks')->get('/youtube/playlist/{playlistId}/tracks', [YoutubeController::class, 'getPlaylistTracks']);
    Route::withoutMiddleware(EnsureYtMusicTokenIsValid::class)->name('.authorize')->get('/youtube/auth/redirect', function () {
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
    Route::withoutMiddleware(EnsureYtMusicTokenIsValid::class)->name('.accessToken')->get('/youtube/auth/access-token', function (Request $request) {
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

        return redirect()->route(session('lastRoute'),session('queryParams'));
    });
});
