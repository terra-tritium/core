<?php

namespace App\Services;

use App\Models\GameMode;
use App\Models\Researched;
use App\Models\NFTConfig;

class GameModeService
{
  public function list($player)
  {
    $isDefence = Researched::where([['player', $player->id], ['code', 800]])->first();
    $isExpansion = Researched::where([['player', $player->id], ['code', 3400]])->first();
    $isWarCompetence = Researched::where([['player', $player->id], ['code', 900]])->first();
    $isSpaceMining = Researched::where([['player', $player->id], ['code', 2000]])->first();
    $isSpaceEngineering = Researched::where([['player', $player->id], ['code', 2800]])->first();
    $isStelarNavigator = Researched::where([['player', $player->id], ['code', 3100]])->first();
    $isWisdom = Researched::where([['player', $player->id], ['code', 3200]])->first();
    $nftConfig = NFTConfig::where('player', $player->id)->first();


    $gameModes = GameMode::orderBy('code')->get();
    $responseGameModes = [];

    foreach ($gameModes as $gm) {
      $requirementMet = true;

      switch ($gm->code) {    
        case 2:
          $requirementMet = $nftConfig ? true : false;
          break;
        case 3:
          $requirementMet = $isWarCompetence ? true : false;
          break;
        case 4:
          $requirementMet = $isWisdom ? true : false;
          break;
        case 5:
          $requirementMet = $isSpaceEngineering ? true : false;
          break;
        case 6:
          $requirementMet = $isDefence ? true : false;
          break;
        case 7:
          $requirementMet = $isExpansion ? true : false;          
          break;
        case 8:
          $requirementMet = $isStelarNavigator ? true : false;
          break;
        case 9:
          $requirementMet = $isSpaceMining ? true : false;
          break;
      }

      $gm->requirementMet = $requirementMet;
      array_push($responseGameModes, $gm);
    }

    return $responseGameModes;
  }
}