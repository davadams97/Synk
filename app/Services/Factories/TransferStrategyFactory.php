<?php

namespace App\Services\Factories;

use App\Interfaces\TransferStrategyInterface;
use App\Services\Strategies\TransferToSpotify;
use App\Services\Strategies\TransferToYTMusic;

class TransferStrategyFactory
{
    public static function create($strategyType): TransferStrategyInterface
    {
        switch ($strategyType) {
            case 'spotify':
                return new TransferToSpotify();
            case 'ytmusic':
                return new TransferToYTMusic();
        }
    }
}
