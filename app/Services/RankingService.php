<?php

namespace App\Services;

use App\Models\Player;

class RankingService
{
  public function addPoints (Player $player, $points) {
    $player->score += $points;
    return $player;
  }
}