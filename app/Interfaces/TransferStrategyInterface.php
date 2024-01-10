<?php

namespace App\Interfaces;

interface TransferStrategyInterface
{
    public function setService($service);

    public function transferPlaylist(array $tracks, string $playlistTitle);
}
