<?php

namespace App\Services;

use App\Models\GameMode;
use App\Models\Researched;

class GameModeService
{
  public function list ($player) {
    $isDefence = Researched::where([['player', $player->id], ['code', 800]])->first();
    $isWarCompetence = Researched::where([['player', $player->id], ['code', 900]])->first();
    $isSpaceMining = Researched::where([['player', $player->id], ['code', 2000]])->first();

    $gameModes = GameMode::orderBy('code')->get();
    $elegibleGameModes = [];

    foreach($gameModes as $gm) {

      switch($gm->code) {
        case 2 :
          if ($isWarCompetence) {
            array_push($elegibleGameModes, $gm);
          }
          break;
        case 5 : 
          if ($isDefence) {
            array_push($elegibleGameModes, $gm);
          }
          break;
        case 8 :
          if ($isSpaceMining) {
            array_push($elegibleGameModes, $gm);
          }
          break;
        default:
          array_push($elegibleGameModes, $gm);
          break;
      }
    }

    return $elegibleGameModes;
  }
}
