<?php

namespace App\Http\Controllers;

use App\Models\NFTConfig;
use App\Models\Player;

class NFTController extends Controller
{
  public function config($slot, $code) {
    $player = Player::getPlayerLogged();
    $nftUserConfig = NFTConfig::where('player', $player->id)->first();
    switch ($slot) {
      case 1:
        $nftUserConfig->slot1 = $code;
        break;
      case 2:
        $nftUserConfig->slot2 = $code;
        break;
      case 3:
        $nftUserConfig->slot3 = $code;
        break;
      case 4:
        $nftUserConfig->slot4 = $code;
        break;
      case 5:
        $nftUserConfig->slot5 = $code;
        break;
    }

    $nftUserConfig->save();
  }
}
