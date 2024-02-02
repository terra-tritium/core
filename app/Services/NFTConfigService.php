<?php

namespace App\Services;

use App\Models\NFTConfig;

class NFTConfigService
{
    public function __construct()
    {

    }

    public function nftConfig($player): NFTConfig
    {
        $nftUserConfig = NFTConfig::where('player', $player->id)->first();

        if (is_null($nftUserConfig)) {
            $nftUserConfig = new NFTConfig();
            $nftUserConfig->player = $player->id;
            $nftUserConfig->save();
        }
        return $nftUserConfig;
    }

}