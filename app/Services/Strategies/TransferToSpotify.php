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

    public function transferPlaylist($tracks, $playlistId, $playlistTitle)
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
            info($playlistIdResponse['id']);
            info($tracksIDsToAdd);
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

    //         foreach ($request['name'] as $song) {
    //             $result = $this->findSongFromSpotify($song);
    //             if (! empty($result)) {
    //                 $songsToAdd[] = $result[0]['item'];
    //             }
    //         }

    //         // info(array_map(fn ($song) => $song['uri'], $songsToAdd));

    //         // info($songsToAdd);
    //         $userProfile = $this->spotifyService->getProfile();
    //         $playlists = $this->spotifyService->getPlaylists();

    //         $filter = [
    //             'keys' => [
    //                 'name',
    //             ],
    //             'shouldSort' => true,
    //         ];
    //         $fuse = new Fuse($playlists, $filter);

    //         // $matchedResult = $fuse->search(['title' => $songName], ['limit' => 1]);
    //         $matchedResult = $fuse->search($request['title'], ['limit' => 1]);

    //         $response = $this->spotifyService->createPlaylist($request['title'], $userProfile['id']);

    //         if ($response->successful()) {
    //             $songsToAddIds = array_map(fn ($song) => $song['uri'], $songsToAdd);
    //             info($matchedResult);
    //             $this->addSongToPlaylist('spotify', $songsToAddIds, $matchedResult[0]['item']['id']);
    //         }

    //         // if ($response->failed()) {
    //         //     $this->deletePlaylist($response);
    //         // }
    //     }
}
