<?php

namespace App\Services\Strategies;

use App\Interfaces\TransferStrategyInterface;
use App\Services\YoutubeMusicService;
use Fuse\Fuse;

class TransferToYTMusic implements TransferStrategyInterface
{
    private YoutubeMusicService $service;

    public function setService($service)
    {
        $this->service = $service;
    }

    public function transferPlaylist($tracks, $playlistId, $playlistTitle)
    {
        $tracksToAdd = [];

        foreach ($tracks as $track) {
            $ytmusicResults = $this->service->searchTracks($track);
            $matchedTrack = $this->filterTracks($ytmusicResults->json(), $track);

            if (! empty($matchedTrack)) {
                $tracksToAdd[] = $matchedTrack[0]['item'];
            }
        }

        $playlistIdResponse = $this->service->createPlaylist($playlistTitle);

        if ($playlistIdResponse->successful()) {
            $tracksIDsToAdd = array_map(fn ($song) => $song['videoId'], $tracksToAdd);
            $this->service->addToPlaylist($playlistIdResponse, $tracksIDsToAdd);
        }

        if ($playlistIdResponse->failed()) {
            $this->service->deletePlaylist($playlistIdResponse);
        }
    }

    private function filterTracks($tracks, $targetTrack)
    {
        $filter = [
            'keys' => [
                'title',
                'artists.name',
                'album.name',
            ],
            'shouldSort' => true,
        ];

        $fuse = new Fuse($tracks, $filter);

        return $fuse->search($targetTrack, ['limit' => 1]);
    }
}
