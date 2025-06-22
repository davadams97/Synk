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
                // Extract track information with better handling of different data structures
                $trackName = null;
                $artist = null;
                $album = null;
                $year = null;
                
                // Handle different track data structures
                if (is_array($track)) {
                    $trackName = $track['name'] ?? $track['title'] ?? null;
                    $artist = $track['artist'] ?? $track['artists'][0]['name'] ?? null;
                    $album = $track['albumName'] ?? $track['album']['name'] ?? null;
                    $year = $track['year'] ?? null;
                } else {
                    $trackName = $track;
                }
                
                // Validate required fields
                if (empty($trackName)) {
                    $failedTracks[] = [
                        'name' => 'Unknown Track',
                        'artist' => $artist ?? 'Unknown Artist',
                        'album' => $album ?? 'Unknown Album',
                        'error' => 'Missing track name'
                    ];
                    continue;
                }
                
                // Try multiple search strategies
                $matchedTrack = $this->findTrackWithFallback($trackName, $artist, $album, $year);

                if (!empty($matchedTrack)) {
                    $tracksToAdd[] = $matchedTrack;
                } else {
                    $failedTracks[] = [
                        'name' => $trackName,
                        'artist' => $artist ?? 'Unknown Artist',
                        'album' => $album ?? 'Unknown Album',
                        'year' => $year
                    ];
                }
            } catch (\Exception $e) {
                // Log the error and continue with next track
                $trackName = is_array($track) ? ($track['name'] ?? $track['title'] ?? 'Unknown') : $track;
                Log::error("Failed to search track '{$trackName}' on Spotify: " . $e->getMessage(), [
                    'track' => $track,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $failedTracks[] = [
                    'name' => $trackName,
                    'error' => $e->getMessage()
                ];
                continue;
            }
        }

        // Only proceed if we have tracks to add
        if (empty($tracksToAdd)) {
            throw new \Exception("No tracks could be found on Spotify");
        }

        $userId = $this->service->getProfile()['id'];

        $playlistIdResponse = $this->service->createPlaylist($playlistTitle, $userId);

        if ($playlistIdResponse->successful()) {
            $tracksIDsToAdd = array_map(fn ($song) => $song['uri'], $tracksToAdd);
            $this->service->addToPlaylist($playlistIdResponse['id'], $tracksIDsToAdd);
        } else {
            throw new \Exception("Failed to create playlist on Spotify");
        }
    }

    private function findTrackWithFallback($trackName, $artist = null, $album = null, $year = null)
    {
        // Strategy 1: Search with track name and artist (most accurate)
        if ($artist) {
            $results = $this->service->searchTracks($trackName, $artist, null, null);
            
            if (!empty($results)) {
                // Try to find the best match with artist validation
                $bestMatch = $this->findBestMatch($results, $trackName, $artist, $album);
                if ($bestMatch) {
                    return $bestMatch;
                }
            }
        }

        // Strategy 2: Search with track name and album
        if ($album) {
            $results = $this->service->searchTracks($trackName, null, $album, null);
            
            if (!empty($results)) {
                // Try to find the best match with album validation
                $bestMatch = $this->findBestMatch($results, $trackName, $artist, $album);
                if ($bestMatch) {
                    return $bestMatch;
                }
            }
        }

        // Strategy 3: Just search by track name (fallback)
        $results = $this->service->searchTracks($trackName, null, null, null);
        
        if (!empty($results)) {
            // Try to find the best match
            $bestMatch = $this->findBestMatch($results, $trackName, $artist, $album);
            if ($bestMatch) {
                return $bestMatch;
            }
        }

        return null;
    }

    private function findBestMatch($results, $trackName, $artist = null, $album = null)
    {
        $bestMatch = null;
        $bestScore = 0;

        foreach ($results as $result) {
            $score = 0;
            $resultArtist = $result['artists'][0]['name'] ?? '';
            $resultAlbum = $result['album']['name'] ?? '';
            $resultName = $result['name'] ?? '';

            // Perfect name match
            if (strtolower($resultName) === strtolower($trackName)) {
                $score += 10;
            }

            // Artist match (case insensitive)
            if ($artist && strtolower($resultArtist) === strtolower($artist)) {
                $score += 8;
            }

            // Album match (case insensitive)
            if ($album && strtolower($resultAlbum) === strtolower($album)) {
                $score += 6;
            }

            // Partial artist match (contains)
            if ($artist && stripos($resultArtist, $artist) !== false) {
                $score += 4;
            }

            // Partial album match (contains)
            if ($album && stripos($resultAlbum, $album) !== false) {
                $score += 3;
            }

            // If we have both artist and album info, prioritize exact matches
            if ($artist && $album && $score >= 20) {
                return $result;
            }

            // Keep track of the best match so far
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $result;
            }
        }

        return $bestMatch;
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
        // For basic algorithm, just return the tracks as-is
        return $tracks;
    }

    private function validateMatch($item, $targetTrack, $artist = null, $album = null, $score)
    {
        $itemTitle = strtolower($item['name'] ?? '');
        $itemArtist = strtolower($item['artists'][0]['name'] ?? '');
        $itemAlbum = strtolower($item['album']['name'] ?? '');
        
        $targetTrackLower = strtolower($targetTrack);
        $artistLower = strtolower($artist ?? '');
        $albumLower = strtolower($album ?? '');

        // If we have artist info, make sure it's a reasonable match
        if ($artist && !empty($artistLower)) {
            // Check if artist names are similar (allowing for slight variations)
            $artistSimilarity = $this->calculateSimilarity($itemArtist, $artistLower);
            if ($artistSimilarity < 0.7) {
                return false;
            }
        }

        // If we have album info, it's a bonus but not required
        if ($album && !empty($albumLower)) {
            $albumSimilarity = $this->calculateSimilarity($itemAlbum, $albumLower);
            if ($albumSimilarity > 0.8) {
                // Boost the score for good album matches
                return true;
            }
        }

        // For very low scores, be more strict
        if ($score > 0.8) {
            return false;
        }

        return true;
    }

    private function calculateSimilarity($str1, $str2)
    {
        // Simple similarity calculation using similar_text
        similar_text($str1, $str2, $percent);
        return $percent / 100;
    }

    private function cleanYouTubeMusicTrackName($trackName)
    {
        // YouTube Music specific patterns that need to be removed
        $patterns = [
            '/\s*\(.*?official.*?video.*?\)/i',
            '/\s*\(.*?audio.*?\)/i',
            '/\s*\(.*?music video.*?\)/i',
            '/\s*\(.*?lyric video.*?\)/i',
            '/\s*\(.*?visualizer.*?\)/i',
            '/\s*\(.*?live.*?\)/i',
            '/\s*\(.*?performance.*?\)/i',
            '/\s*\(.*?cover.*?\)/i',
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
            '/\s*\[.*?official.*?video.*?\]/i',
            '/\s*\[.*?audio.*?\]/i',
            '/\s*\[.*?music video.*?\]/i',
            '/\s*\[.*?lyric video.*?\]/i',
            '/\s*\[.*?visualizer.*?\]/i',
            '/\s*\[.*?live.*?\]/i',
            '/\s*\[.*?performance.*?\]/i',
            '/\s*\[.*?cover.*?\]/i',
            '/\s*\[.*?remix.*?\]/i',
            '/\s*\[.*?feat.*?\]/i',
            '/\s*\[.*?ft.*?\]/i',
            '/\s*\[.*?featuring.*?\]/i',
            '/\s*\[.*?radio edit.*?\]/i',
            '/\s*\[.*?explicit.*?\]/i',
            '/\s*\[.*?clean.*?\]/i',
            '/\s*\[.*?album version.*?\]/i',
            '/\s*\[.*?single version.*?\]/i',
            '/\s*\[.*?extended.*?\]/i',
            '/\s*\[.*?short.*?\]/i',
            '/\s*\[.*?edit.*?\]/i',
            '/\s*\[.*?mix.*?\]/i',
            '/\s*\[.*?version.*?\]/i',
        ];

        $cleaned = preg_replace($patterns, '', $trackName);
        return trim($cleaned);
    }

    private function simplifyTrackName($trackName)
    {
        // Remove common words that might not be in the original track name
        $wordsToRemove = [
            'official', 'video', 'audio', 'music', 'lyric', 'visualizer', 'live', 'performance', 'cover',
            'remix', 'feat', 'ft', 'featuring', 'radio', 'edit', 'explicit', 'clean', 'album', 'single',
            'extended', 'short', 'mix', 'version', 'track', 'song'
        ];

        $words = explode(' ', strtolower($trackName));
        $filteredWords = array_filter($words, function($word) use ($wordsToRemove) {
            return !in_array($word, $wordsToRemove);
        });

        return implode(' ', $filteredWords);
    }

    private function getShortTrackName($trackName)
    {
        // Get just the first 2-3 words of the track name
        $words = explode(' ', $trackName);
        if (count($words) <= 3) {
            return $trackName; // Already short enough
        }
        
        return implode(' ', array_slice($words, 0, 3));
    }

    private function extractArtistFromTitle($trackName)
    {
        // Common patterns for artist names in track titles
        $patterns = [
            '/^(.+?)\s*[-–—]\s*(.+)$/', // "Artist - Track" or "Artist – Track"
            '/^(.+?)\s*:\s*(.+)$/', // "Artist: Track"
            '/^(.+?)\s*feat\.?\s*(.+)$/i', // "Artist feat. Track"
            '/^(.+?)\s*ft\.?\s*(.+)$/i', // "Artist ft. Track"
            '/^(.+?)\s*featuring\s*(.+)$/i', // "Artist featuring Track"
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $trackName, $matches)) {
                $artist = trim($matches[1]);
                // Don't extract if the artist part is too long (likely not an artist name)
                if (strlen($artist) < 50 && !empty($artist)) {
                    return $artist;
                }
            }
        }

        return null;
    }

    private function generateTrackNameVariations($trackName)
    {
        $variations = [];
        
        // Remove common prefixes/suffixes
        $cleaned = preg_replace('/^(the\s+)/i', '', $trackName);
        if ($cleaned !== $trackName) {
            $variations[] = $cleaned;
        }
        
        // Try without "The" prefix
        if (preg_match('/^the\s+(.+)$/i', $trackName, $matches)) {
            $variations[] = $matches[1];
        }
        
        // Try with and without apostrophes
        $noApostrophe = str_replace("'", "", $trackName);
        if ($noApostrophe !== $trackName) {
            $variations[] = $noApostrophe;
        }
        
        // Try with and without "and" vs "&"
        $withAnd = str_replace(" & ", " and ", $trackName);
        if ($withAnd !== $trackName) {
            $variations[] = $withAnd;
        }
        
        $withAmpersand = str_replace(" and ", " & ", $trackName);
        if ($withAmpersand !== $trackName) {
            $variations[] = $withAmpersand;
        }
        
        return array_unique($variations);
    }
}
