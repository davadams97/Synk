<?php

namespace App\Http\Middleware;

use Closure;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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

            if (now()->addMinutes(10)->timestamp < $expiryTime) {
                return $next($request);
            }

            $response = $this->getSpotifyRefreshToken();

            session(
                [
                    'spotifyAccessToken' => $response->json('access_token'),
                    'spotifyRefreshToken' => $response->json('refresh_token'),
                    'spotifyExpiresAt' => now()->addSeconds($response->json('expires_in'))->timestamp,
                ]
            );
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
            ]);
    }
}
