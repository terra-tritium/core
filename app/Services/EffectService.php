<?php

namespace App\Services;

use App\Models\Effect;
use App\Models\GameMode;
use App\Models\Planet;
use App\Models\Player;

class EffectService
{

/**
 * Recupera percentual  para desconto no valor da build
 */
  private function getPercentDiscountBuildEffect($player)
  {
    $discountBuild = 0;
    if ($player->gameMode == GameMode::MODE_COLONIZER || $player->gameMode == GameMode::MODE_BUILDER) {
      $effect = Effect::where('player', $player->id)->firstOrFail();
      $discountBuild = $effect->discountBuild;
    }
    return $discountBuild;
  }

  /**
   * Recupera percentual de  velocidade de mineração
   */
  private function getPercentSpeedMiningEffect($player)
  {
    $percentMining = 0;
    if ($player->gameMode == GameMode::MODE_SPACE_TITAN || $player->gameMode == GameMode::MODE_MINER) {
      $effect = Effect::where('player', $player->id)->firstOrFail();
      $percentMining = $effect->speedMining;
    }
    return $percentMining;
  }
  /**
   * Recupera percentual de velocidade de construção de robos
   */
  private function getPercentRobotConstructionSpeed($playerId){
    $player = Player::where('id', $playerId)->firstOrFail();
    $percentConstructionSpeed = 0;
    if($player->gameMode == GameMode::MODE_SPACE_TITAN ||
       $player->gameMode == GameMode::MODE_BUILDER || 
       $player->gameMode == GameMode::MODE_NAVIGATOR
      ){
      
        $effect = Effect::where('player', $player->id)->firstOrFail();
        $percentConstructionSpeed = $effect->speedProduceUnit;
    }
    return $percentConstructionSpeed;
  }
  /**
   * Recupera percentual de velocidade da viagem
   */
  private function getPercenteTravelSpeed($player){
    $percentTravelSpeed = 0;
    if($player->gameMode == GameMode::MODE_NAVIGATOR){
        $effect = Effect::where('player', $player->id)->firstOrFail();
        $percentTravelSpeed = $effect->speedTravel;
    }
    return $percentTravelSpeed;
  }
  /**
   * Recupera percentual de velocidade de construção de naves
   */
  private function getPercentShipConstructionSpeed($playerId){
    $player = Player::where('id', $playerId)->firstOrFail();
    $percentConstructionSpeed = 0;    
    if($player->gameMode == GameMode::MODE_ENGINEER ||
       $player->gameMode == GameMode::MODE_BUILDER || 
       $player->gameMode == GameMode::MODE_NAVIGATOR
      ){
      
        $effect = Effect::where('player', $player->id)->firstOrFail();
        $percentConstructionSpeed = $effect->speedProduceShip;
    }
    return $percentConstructionSpeed;
  }
  /**
   * Recupera percentual develocidade de construção de builds
   */
  private function getPercentConstructionBuildSpeed($player){
    $percentConstructionBuildSpeed = 0;    
    if($player->gameMode == GameMode::MODE_RESEARCHER){
        $effect = Effect::where('player', $player->id)->firstOrFail();
        $percentConstructionBuildSpeed = $effect->speedConstructionBuild;
    }
    return $percentConstructionBuildSpeed;
  }
  private function getPercentResearcherSpeed($player){
    $percentResearcherSpeed = 0;
    if($player->gameMode == GameMode::MODE_RESEARCHER || 
       $player->gameMode == GameMode::MODE_SPACE_TITAN || 
       $player->gameMode == GameMode::MODE_ENGINEER
      ){
      $effect = Effect::where('player', $player->id)->firstOrFail();
      $percentResearcherSpeed = $effect->speedResearch;
    }
    return $percentResearcherSpeed;
  }
  /**
   * Bonificação de proteção
   */
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
    $discount = $this->getPercentDiscountBuildEffect($player);
    return floor($value + (($value * $discount) / 100));
  }
  public function calcMiningSpeed($value, $player)
  {
    $percentSpeed = $this->getPercentSpeedMiningEffect($player);
    return $value + (($value * $percentSpeed) / 100);
  }
  public function calcAttack($value, $player)
  {
    return 1;
  }
  public function calcResearchSpeed($value, $player)
  {
    $percent = $this->getPercentResearcherSpeed($player) * -1;
    return floor($value + (($value * $percent) / 100)); 
  }
  public function calcTravelSpeed($value, $player){
    $percent = $this->getPercenteTravelSpeed($player) * -1;
    return floor($value + (($value * $percent) / 100)); 
  }

  public function calcConstructionBuildSpeed($value, $player){
    $percent = $this->getPercentConstructionBuildSpeed($player) * -1;
    return floor($value + (($value * $percent) / 100)); 
  }

  public function calcShipsConstructSpeed($value, $player){
    $percent = $this->getPercentShipConstructionSpeed($player) * -1;
    return floor($value + (($value * $percent) / 100)); 
  }
  public function calcRobotConstructSpeed($value, $player)
  {
    $percent = $this->getPercentRobotConstructionSpeed($player) * -1;
    return floor($value + (($value * $percent) / 100));
  }

  public function applyEffect($player, $code)
  {
    $effect = Effect::where("player", $player->id)->first();
    $planet = Planet::where("player",$player->id)->first();
    $researchService = new ResearchService();
    $planetService = new PlanetService();
    if (!$effect) {
      $effect = new Effect();
      $effect->player = $player->id;
    }
    #zerar os atributos dos efeitos para receber os novos
    #Salva os valores de metal, cristal, uranium e pesquisa, salva o novo tempo de contagem
    #Como a mineração tem como base o tempo inicial, salva o novo momento pois cada modo de jogo 
    #pode alterar a contagem 
    if ($code > 0 && $code <= 9) {
      $effect->zerar();
      $planet = $planetService->addMetal($planet, 0);
      $planet = $planetService->addCrystal($planet,0);
      $planet = $planetService->addUranium($planet,0);
      // $researchService->playerSincronize($player);
      $planet->save(); 
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
        $effect->speedConstructionBuild = -20;
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
