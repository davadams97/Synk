<?php

namespace App\Http\Middleware;

use Closure;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureSpotifyTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (str_contains($request->url(), 'spotify')) {
            $expiryTime = session('spotifyExpiresAt');
            $currentTime = now()->timestamp;

            // Check if token is expired or will expire within 5 minutes
            if (!$expiryTime || $currentTime >= ($expiryTime - 300)) {
                // Token is expired or will expire soon, refresh it
                $response = $this->getSpotifyRefreshToken();
                
                if ($response->successful()) {
                    session([
                        'spotifyAccessToken' => $response->json('access_token'),
                        'spotifyRefreshToken' => $response->json('refresh_token') ?? session('spotifyRefreshToken'),
                        'spotifyExpiresAt' => now()->addSeconds($response->json('expires_in'))->timestamp,
                    ]);
                } else {
                    // Refresh failed, redirect to re-authorize
                    return redirect()->route('spotify.authorize');
                }
            }
        }

        return $next($request);
    }

    private function getSpotifyRefreshToken(): PromiseInterface|\Illuminate\Http\Client\Response
    {
        $refreshToken = session('spotifyRefreshToken');

        return Http::asForm()
            ->post('https://accounts.spotify.com/api/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
                'client_id' => env('SPOTIFY_CLIENT_ID'),
                'client_secret' => env('SPOTIFY_CLIENT_SECRET'),
            ]);
    }
}
