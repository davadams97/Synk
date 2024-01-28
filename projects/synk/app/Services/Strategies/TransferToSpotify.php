<?php

namespace App\Services\Strategies;

use App\Interfaces\TransferStrategyInterface;
use App\Services\SpotifyService;
use Fuse\Fuse;

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

        foreach ($tracks as $track) {
            $spotifyResults = $this->service->searchTracks($track);
            $matchedTrack = $this->filterTracks($spotifyResults, $track);

            if (! empty($matchedTrack)) {
                $tracksToAdd[] = $matchedTrack[0]['item'];
            }
        }

        $userId = $this->service->getProfile()['id'];

        $playlistIdResponse = $this->service->createPlaylist($playlistTitle, $userId);

        if ($playlistIdResponse->successful()) {
            $tracksIDsToAdd = array_map(fn ($song) => $song['uri'], $tracksToAdd);
            $this->service->addToPlaylist($playlistIdResponse['id'], $tracksIDsToAdd);
        }
    }

    private function filterTracks($tracks, $targetTrack)
    {
        $filter = [
            'keys' => [
                'name',
                'artists.name',
                'album.name',
            ],
            'shouldSort' => true,
        ];

        $fuse = new Fuse($tracks, $filter);

        return $fuse->search($targetTrack, ['limit' => 1]);
    }
}
