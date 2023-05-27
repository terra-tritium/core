<?php

namespace App\Services;

use App\Models\Player;

class RankingService
{
  public function addPoints ($points) {
    $user = auth()->user()->id;
    $player = Player::where("user", $user)->firstOrFail();
    $player->score += $points;
    $player->save();
  }
}