<?php

namespace App\Services;

use App\Models\NFTConfig;

class NFTConfigService
{
    public function __construct () {

    }

    /**
     * @param $player
     * @return NFTConfig
     */
    public function nftConfig($player): NFTConfig
    {
        return NFTConfig::firstOrCreate(['player' => $player->id]);
    }

}
