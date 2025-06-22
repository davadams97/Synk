<?php

namespace App\Services\Strategies;

use App\Interfaces\TransferStrategyInterface;
use App\Services\SpotifyService;
use Fuse\Fuse;
use Illuminate\Support\Facades\Log;

class TransferToSpotify implements TransferStrategyInterface
{
    protected SpotifyService $service;

    public function setService($service)
    {
        $this->service = $service;
    }

    public function transferPlaylist($tracks, $playlistTitle)
    {
        $tracksToAdd = [];
        $failedTracks = [];

        foreach ($tracks as $track) {
            try {
                // Extract track information
                $trackName = $track['name'] ?? $track;
                $artist = $track['artist'] ?? null;
                $album = $track['albumName'] ?? null;
                $year = $track['year'] ?? null;
                
                Log::info("Attempting to match track", [
                    'track_name' => $trackName,
                    'artist' => $artist,
                    'album' => $album,
                    'year' => $year
                ]);
                
                // Try multiple search strategies
                $matchedTrack = $this->findTrackWithFallback($trackName, $artist, $album, $year);

                if (!empty($matchedTrack)) {
                    $tracksToAdd[] = $matchedTrack;
                    Log::info("Successfully matched track: {$trackName}", [
                        'artist' => $artist,
                        'album' => $album,
                        'matched_title' => $matchedTrack['name'] ?? 'Unknown',
                        'matched_artist' => $matchedTrack['artists'][0]['name'] ?? 'Unknown'
                    ]);
                } else {
                    $failedTracks[] = [
                        'name' => $trackName,
                        'artist' => $artist,
                        'album' => $album,
                        'year' => $year
                    ];
                    Log::warning("No match found for track: {$trackName}", [
                        'artist' => $artist,
                        'album' => $album,
                        'year' => $year
                    ]);
                }
            } catch (\Exception $e) {
                // Log the error and continue with next track
                $trackName = is_array($track) ? ($track['name'] ?? 'Unknown') : $track;
                Log::error("Failed to search track '{$trackName}' on Spotify: " . $e->getMessage());
                $failedTracks[] = [
                    'name' => $trackName,
                    'error' => $e->getMessage()
                ];
                continue;
            }
        }

        // Log summary
        Log::info("Transfer summary", [
            'total_tracks' => count($tracks),
            'successful_matches' => count($tracksToAdd),
            'failed_matches' => count($failedTracks),
            'failed_tracks' => $failedTracks
        ]);

        // Only proceed if we have tracks to add
        if (empty($tracksToAdd)) {
            throw new \Exception("No tracks could be found on Spotify");
        }

        $userId = $this->service->getProfile()['id'];

        $playlistIdResponse = $this->service->createPlaylist($playlistTitle, $userId);

        if ($playlistIdResponse->successful()) {
            $tracksIDsToAdd = array_map(fn ($song) => $song['uri'], $tracksToAdd);
            $this->service->addToPlaylist($playlistIdResponse['id'], $tracksIDsToAdd);
        }
    }

    private function findTrackWithFallback($trackName, $artist = null, $album = null, $year = null)
    {
        // Strategy 1: Full search with all parameters
        if ($artist && $album) {
            $results = $this->service->searchTracks($trackName, $artist, $album, $year);
            if (!empty($results)) {
                $matched = $this->filterTracks($results, $trackName, $artist, $album, 0.3);
                if (!empty($matched)) {
                    return $matched[0]['item'];
                }
            }
        }

        // Strategy 2: Search with track name and artist only
        if ($artist) {
            $results = $this->service->searchTracks($trackName, $artist, null, $year);
            if (!empty($results)) {
                $matched = $this->filterTracks($results, $trackName, $artist, null, 0.4);
                if (!empty($matched)) {
                    return $matched[0]['item'];
                }
            }
        }

        // Strategy 3: Search with track name and album only
        if ($album) {
            $results = $this->service->searchTracks($trackName, null, $album, $year);
            if (!empty($results)) {
                $matched = $this->filterTracks($results, $trackName, null, $album, 0.4);
                if (!empty($matched)) {
                    return $matched[0]['item'];
                }
            }
        }

        // Strategy 4: Search with just track name (most permissive)
        $results = $this->service->searchTracks($trackName, null, null, $year);
        if (!empty($results)) {
            $matched = $this->filterTracks($results, $trackName, null, null, 0.5);
            if (!empty($matched)) {
                return $matched[0]['item'];
            }
        }

        // Strategy 5: Try with cleaned track name (remove special characters, remix indicators, etc.)
        $cleanedTrackName = $this->cleanTrackName($trackName);
        if ($cleanedTrackName !== $trackName) {
            $results = $this->service->searchTracks($cleanedTrackName, $artist, $album, $year);
            if (!empty($results)) {
                $matched = $this->filterTracks($results, $cleanedTrackName, $artist, $album, 0.4);
                if (!empty($matched)) {
                    return $matched[0]['item'];
                }
            }
        }

        return null;
    }

    private function cleanTrackName($trackName)
    {
        // Remove common suffixes that might interfere with matching
        $patterns = [
            '/\s*\(.*?remix.*?\)/i',
            '/\s*\(.*?feat.*?\)/i',
            '/\s*\(.*?ft.*?\)/i',
            '/\s*\(.*?featuring.*?\)/i',
            '/\s*\(.*?radio edit.*?\)/i',
            '/\s*\(.*?explicit.*?\)/i',
            '/\s*\(.*?clean.*?\)/i',
            '/\s*\(.*?album version.*?\)/i',
            '/\s*\(.*?single version.*?\)/i',
            '/\s*\(.*?extended.*?\)/i',
            '/\s*\(.*?short.*?\)/i',
            '/\s*\(.*?edit.*?\)/i',
            '/\s*\(.*?mix.*?\)/i',
            '/\s*\(.*?version.*?\)/i',
        ];

        $cleaned = preg_replace($patterns, '', $trackName);
        return trim($cleaned);
    }

    private function filterTracks($tracks, $targetTrack, $artist = null, $album = null, $threshold = 0.3)
    {
        // Check if tracks is null or empty
        if (empty($tracks) || !is_array($tracks)) {
            return [];
        }

        // Build multiple search queries for better matching
        $searchQueries = [$targetTrack];
        
        if ($artist) {
            $searchQueries[] = "$targetTrack $artist";
            $searchQueries[] = "$artist $targetTrack";
        }
        
        if ($album) {
            $searchQueries[] = "$targetTrack $album";
        }
        
        if ($artist && $album) {
            $searchQueries[] = "$targetTrack $artist $album";
            $searchQueries[] = "$artist $targetTrack $album";
        }

        $filter = [
            'keys' => [
                'name',
                'artists.name',
                'album.name',
            ],
            'shouldSort' => true,
            'threshold' => $threshold,
            'includeScore' => true,
        ];

        $fuse = new Fuse($tracks, $filter);
        $bestMatch = null;
        $bestScore = 1.0;

        // Try each search query and keep the best match
        foreach ($searchQueries as $query) {
            $results = $fuse->search($query, ['limit' => 3]);
            
            foreach ($results as $result) {
                $score = $result['score'] ?? 1.0;
                if ($score < $bestScore) {
                    $bestScore = $score;
                    $bestMatch = $result;
                }
            }
        }

        return $bestMatch ? [$bestMatch] : [];
    }
}
