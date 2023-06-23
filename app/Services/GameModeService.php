<?php

namespace App\Services;

use App\Models\GameMode;
use App\Models\Researched;

class GameModeService
{
  public function list ($player) {
    $isDefenceResearch = Researched::where([['player', $player->id], ['code', 800]])->first();

    $gameModes = GameMode::orderBy('code')->get();
    $elegibleGameModes = [];

    foreach($gameModes as $gm) {
      if ($gm->code == 5) {
        # Research defence include protector mode
        if ($isDefenceResearch) {
          array_push($elegibleGameModes, $gm);
        }
      } else {
        array_push($elegibleGameModes, $gm);
      }
    }

    return $elegibleGameModes;
  }
}
