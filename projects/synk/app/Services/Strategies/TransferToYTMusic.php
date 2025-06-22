<?php

namespace App\Services\Strategies;

use App\Interfaces\TransferStrategyInterface;
use App\Services\YoutubeMusicService;
use Fuse\Fuse;
use Illuminate\Support\Facades\Log;

class TransferToYTMusic implements TransferStrategyInterface
{
    protected YoutubeMusicService $service;

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
                        'matched_title' => $matchedTrack['title'] ?? 'Unknown',
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
                Log::error("Failed to search track '{$trackName}' on YouTube Music: " . $e->getMessage());
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
            throw new \Exception("No tracks could be found on YouTube Music");
        }

        $playlistIdResponse = $this->service->createPlaylist($playlistTitle);

        if ($playlistIdResponse->successful()) {
            $tracksIDsToAdd = array_map(fn ($song) => $song['videoId'], $tracksToAdd);
            $this->service->addToPlaylist($playlistIdResponse, $tracksIDsToAdd);
        }
    }

    private function findTrackWithFallback($trackName, $artist = null, $album = null, $year = null)
    {
        Log::info("Starting search strategies for track: {$trackName}", [
            'artist' => $artist,
            'album' => $album,
            'year' => $year
        ]);

        // Strategy 1: Full search with all parameters
        if ($artist && $album) {
            Log::info("Strategy 1: Full search with all parameters");
            $results = $this->service->searchTracks($trackName, $artist, $album, $year);
            if ($results && $results->successful()) {
                $matched = $this->filterTracks($results->json(), $trackName, $artist, $album, 0.3);
                if (!empty($matched)) {
                    Log::info("Strategy 1 successful", ['matched_title' => $matched[0]['item']['title'] ?? 'Unknown']);
                    return $matched[0]['item'];
                }
            }
        }

        // Strategy 2: Search with track name and artist only
        if ($artist) {
            Log::info("Strategy 2: Search with track name and artist only");
            $results = $this->service->searchTracks($trackName, $artist, null, $year);
            if ($results && $results->successful()) {
                $matched = $this->filterTracks($results->json(), $trackName, $artist, null, 0.4);
                if (!empty($matched)) {
                    Log::info("Strategy 2 successful", ['matched_title' => $matched[0]['item']['title'] ?? 'Unknown']);
                    return $matched[0]['item'];
                }
            }
        }

        // Strategy 3: Search with track name and album only
        if ($album) {
            Log::info("Strategy 3: Search with track name and album only");
            $results = $this->service->searchTracks($trackName, null, $album, $year);
            if ($results && $results->successful()) {
                $matched = $this->filterTracks($results->json(), $trackName, null, $album, 0.4);
                if (!empty($matched)) {
                    Log::info("Strategy 3 successful", ['matched_title' => $matched[0]['item']['title'] ?? 'Unknown']);
                    return $matched[0]['item'];
                }
            }
        }

        // Strategy 4: Search with just track name (most permissive)
        Log::info("Strategy 4: Search with just track name");
        $results = $this->service->searchTracks($trackName, null, null, $year);
        if ($results && $results->successful()) {
            $matched = $this->filterTracks($results->json(), $trackName, null, null, 0.5);
            if (!empty($matched)) {
                Log::info("Strategy 4 successful", ['matched_title' => $matched[0]['item']['title'] ?? 'Unknown']);
                return $matched[0]['item'];
            }
        }

        // Strategy 5: Try with cleaned track name (remove special characters, remix indicators, etc.)
        $cleanedTrackName = $this->cleanTrackName($trackName);
        if ($cleanedTrackName !== $trackName) {
            Log::info("Strategy 5: Search with cleaned track name", ['cleaned_name' => $cleanedTrackName]);
            $results = $this->service->searchTracks($cleanedTrackName, $artist, $album, $year);
            if ($results && $results->successful()) {
                $matched = $this->filterTracks($results->json(), $cleanedTrackName, $artist, $album, 0.4);
                if (!empty($matched)) {
                    Log::info("Strategy 5 successful", ['matched_title' => $matched[0]['item']['title'] ?? 'Unknown']);
                    return $matched[0]['item'];
                }
            }
        }

        Log::warning("All search strategies failed for track: {$trackName}");
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
                'title',
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
