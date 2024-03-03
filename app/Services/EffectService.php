<?php

namespace App\Services;

use App\Models\Effect;
use App\Models\GameMode;

class EffectService
{


  private function getDiscountBuildEffect($player)
  {
    $discountBuild = 0;
    if ($player->gameMode == GameMode::MODE_COLONIZER || $player->gameMode == GameMode::MODE_BUILDER) {
      $effect = Effect::where('player', $player->id)->firstOrFail();
      $discountBuild = $effect->discountBuild;
    }
    return $discountBuild;
  }

  private function getSpeedMiningEffect($player)
  {
    $percentMining = 0;
    if ($player->gameMode == GameMode::MODE_SPACE_TITAN || $player->gameMode == GameMode::MODE_MINER) {
      $effect = Effect::where('player', $player->id)->firstOrFail();
      $percentMining = $effect->speedMining;
    }
    return $percentMining;
  }
  public function getProtectionBonus($player)
  {
    $protectionBonus = 0;
    if (
      $player->gameMode == GameMode::MODE_COLONIZER ||
      $player->gameMode == GameMode::MODE_PROTECTOR ||
      $player->gameMode == GameMode::MODE_MINER
    ) {
      $effect = Effect::where('player', $player->id)->firstOrFail();
      $protectionBonus = $effect->protect;
    }
    return $protectionBonus;
  }
  /**
   * @todo
   */
  public function calcProtection($value, $player)
  {
    $bonus = $this->getProtectionBonus($player);
    return floor($value + (($value * $bonus) / 100));
  }

  public function calcDiscountBuild($value, $player)
  {
    $discount = $this->getDiscountBuildEffect($player);
    return floor($value + (($value * $discount) / 100));
  }
  public function calcMiningSpeed($value, $player)
  {
    $percentSpeed = $this->getSpeedMiningEffect($player);
    return $value + (($value * $percentSpeed) / 100);
  }
  public function calcAttack($value, $player)
  {
    return 1;
  }
  public function calcResearchSpeed($value, $player)
  {
    return 1;
  }
  public function calcRobotConstructSpeed($value, $player)
  {
    return 1;
  }

  public function applyEffect($player, $code)
  {
    $effect = Effect::where("player", $player)->first();
    if (!$effect) {
      $effect = new Effect();
      $effect->player = $player;
    }
    #zerar os atributos dos efeitos para receber os novos
    if ($code > 0 && $code <= 9) {
      $effect->zerar();
    }

    switch ($code) {
      case GameMode::MODE_CONQUER:
        $effect->zerar();
        $effect->save();
        break;
        #NFT     
      case GameMode::MODE_COLONIZER:
        $effect->discountBuild = -10;
        $effect->protect = 10;
        break;
        #Space Titan 
      case GameMode::MODE_SPACE_TITAN:
        $effect->speedProduceUnit = 20;
        $effect->extraAttack = 2;
        $effect->speedResearch = -20;
        $effect->speedMining = -5;
        break;
        # Researcher
      case GameMode::MODE_RESEARCHER:
        $effect->speedResearch = 20;
        $effect->costBuild = 20;
        break;
        # Engineer
      case GameMode::MODE_ENGINEER:
        $effect->speedProduceShip = 20;
        $effect->speedResearch = -20;
        break;
        # Protector
      case GameMode::MODE_PROTECTOR:
        $effect->protect = 20;
        break;
        # Builder
      case GameMode::MODE_BUILDER:
        $effect->costBuild = -20;
        $effect->speedProduceShip = -20;
        $effect->speedProduceUnit = -20;
        break;
        # Navigator
      case GameMode::MODE_NAVIGATOR:
        $effect->speedTravel = 20;
        $effect->speedProduceShip = -20;
        $effect->speedProduceUnit = -20;
        break;
        # Miner
      case GameMode::MODE_MINER:
        $effect->speedMining = 2;
        $effect->protect = -20;
        break;
    }
    $effect->save();
  }
}
