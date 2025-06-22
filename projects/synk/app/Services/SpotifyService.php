<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SpotifyService
{
    public function getProfile()
    {
        return Http::spotify()->get('/me')->json();
    }

    public function getPlaylists()
    {
        return Http::spotify()->get('/me/playlists')['items'];
    }

    public function getPlaylist($playlistId)
    {
        return Http::spotify()->get('/playlists/'.$playlistId)->json();
    }

    public function getPlaylistTracks($playlistId)
    {
        $queryParams = ['fields' => 'items(track(name,id,uri,artists(name),album(name, images)))'];

        return Http::spotify()->get('/playlists/'.$playlistId.'/tracks', $queryParams)['items'];
    }

    public function createPlaylist($name, $userId)
    {
        return Http::spotify()->post(
            '/users/'.$userId.'/playlists',
            [
                'name' => $name,
            ]
        );
    }

    public function addToPlaylist($playlistId, $trackIds)
    {
        return Http::spotify()->post(
            '/playlists/'.$playlistId.'/tracks',
            ['uris' => $trackIds, 'position' => 0]
        );
    }

    public function searchTracks($query, $artist = null, $album = null, $year = null)
    {
        // Clean and validate the query
        $query = trim($query);
        if (empty($query)) {
            return [];
        }

        // Build a more comprehensive search query
        $searchQuery = $query;
        
        if ($artist) {
            $artist = trim($artist);
            if (!empty($artist)) {
                $searchQuery .= " artist:$artist";
            }
        }
        
        if ($album) {
            $album = trim($album);
            if (!empty($album)) {
                $searchQuery .= " album:$album";
            }
        }
        
        if ($year) {
            $year = trim($year);
            if (!empty($year) && is_numeric($year)) {
                $searchQuery .= " year:$year";
            }
        }

        try {
            $response = Http::spotify()->get(
                '/search',
                [
                    'q' => $searchQuery,
                    'type' => 'track',
                    'limit' => 20, // Increase limit for better matching
                ]
            );

            if ($response->successful()) {
                $data = $response->json();
                $tracks = $data['tracks']['items'] ?? [];
                return $tracks;
            } else {
                return [];
            }
        } catch (\Exception $e) {
            return [];
        }
    }

    public function unfollowPlaylist($playlistId)
    {
        return Http::spotify()->delete('/playlist/'.$playlistId.'/followers');
    }
}
