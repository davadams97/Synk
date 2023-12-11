<?php

namespace App\Services\Strategies;

use App\Interfaces\TransferStrategyInterface;
use App\Services\SpotifyService;
use Fuse\Fuse;

class TransferToSpotify implements TransferStrategyInterface
{
    protected SpotifyService $spotifyService;

    public function setService($service)
    {
        $this->spotifyService = $service;
    }

    public function transferPlaylist($tracks, $playlistId, $playlistTitle)
    {
        $songsToAdd = [];

        foreach ($tracks as $track) {
            $spotifyResults = $this->spotifyService->searchTracks($track);
            $matchedTrack = $this->filterTracks($spotifyResults, $track);

            if (! empty($matchedTrack)) {
                $songsToAdd[] = $matchedTrack[0]['item'];
            }
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
