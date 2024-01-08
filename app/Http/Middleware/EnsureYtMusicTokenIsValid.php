<?php

namespace App\Http\Middleware;

use Closure;
use Google\Service\YouTube;
use Google\Client;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class EnsureYtMusicTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (str_contains($request->url(), 'youtube')) {
            $expiryTime = session('ytMusicExpiresAt');

            if (now()->addMinutes(10)->timestamp < $expiryTime) {
                return $next($request);
            }

            $response = $this->getYtMusicRefreshToken();

            session(
                [
                    'ytMusicAccessToken' => $response['access_token'],
                    'ytMusicRefreshToken' => $response['refresh_token'],
                    'ytMusicExpiresAt' => now()->addSeconds($response['expires_in'])->timestamp,
                ]
            );
        }

        return $next($request);
    }

    private function getYtMusicRefreshToken(): array
    {
        $refreshToken = session('ytMusicRefreshToken');

        $client = new Client();
        $client->setAuthConfig(json_decode(env('GOOGLE_CLIENT_SECRET'), true));
        $client->addScope(YOUTUBE::class::YOUTUBE_FORCE_SSL);

        return $client->fetchAccessTokenWithRefreshToken($refreshToken);
    }
}
