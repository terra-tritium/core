<?php

namespace App\Services;

use App\Jobs\LandBattleJob;
use App\Models\Battle;
use App\Models\Travel;
use App\Models\Planet;

class LandBattleService
{
  public function start ($planetId, $travelId) {
    $battle = new Battle([
      'planet' => $planetId,
      'status' => 1,
      'attackDemage' => null,
      'defenseDemage' => null,
      'attackUnits' => null,
      'defenseUnits' => null,
      'result' => null,
      'start' => now()->timestamp,
      'stage' => 1,
      'resources' => null
    ]);
    $battle->save();
    $travel = Travel::find($travelId);
    $planet = Planet::find($planetId);
    $this->loadAttackTroops($travel, $battle);
    $this->loadDefenseTroops(null, $battle);
    $this->startStage($battle);
  }

  public function join ($side, $battle, $travler) {
    if ($side == 'attack') {
      $this->loadAttackTroops($travler, $battle);
    } else {
      $this->loadDefenseTroops($travler, $battle);
    }
  }

  public function loadAttackTroops ($travel, $battle) {
    $battle->addAttackUnits($travel->troop);
    $battle->save();
  }

  public function loadDefenseTroops ($travel, $battle) {
    if ($travel == null) {
      $planet = Planet::find($battle->planet);
      $battle->addDefenseUnits($planet->troops);
    } else {
      $battle->addDefenseUnits($travel->troop);
    }
    $battle->save();
  }

  public function startStage ($battle) {
    LandBattleJob::dispatch(
      $this,
      $battle
    )->delay(now()->addSeconds(5));
  }

  public function runStage($battleId, $side) {
    $battle = Battle::find($battleId);
    $aForce = $this->calcAttackForce($side);
    $aForce += $this->applyAttackEffects($side);

    $dForce = $this->calcDefenseForce($side);
    $dForce += $this->applyDefenseEffects($side);

    $aForce += $this->resolveStrategy('attack');
    $dForce += $this->resolveStrategy('defense');

    $demage = $this->calcDemage($aForce, $dForce);

    $shielAffected = $this->hitShield($demage);
    $demage = $demage - $shielAffected;
    $this->hitTroops($battle, $side, $demage);

    if ($side == 'defense' && !$this->checkEnd()) {
      LandBattleJob::dispatch(
          $this,
          $battleId
      )->delay(now()->addSeconds(env("TRITIUM_STAGE_SPEED")));
    }

    # recebe novos viajantes que chegaram ao planeta
    $travels = $this->getTravelers($side, $battle->planet);
    if ($travels) {
      foreach ($travels as $member) {
        $this->join($side, $battle, $member);
      }
    }
  }

  public function calcAttackForce ($battle) {
    $force = 0;
    $aZone = $this->loadFigthZone($battle, 'attack');
    foreach ($aZone as $unit) {
      $force += $unit->attack;
    }
    return $force;
  }

  public function calcDefenseForce ($battle) {
    $force = 0;
    $aZone = $this->loadFigthZone($battle, 'defense');
    foreach ($aZone as $unit) {
      $force += $unit->defense;
    }
    return $force;
  }

  public function applyAttackEffects () {
    return 0;
  }

  public function applyDefenseEffects () {
    return 0;
  }

  public function resolveStrategy ($side) {
    return 0;
  }

  public function calcDemage ($aForce, $dForce) {
    return $aForce - $dForce;
  }

  public function hitShield () {
    return 0;
  }

  public function hitTroops () {

  }

  public function isEmptyTroops () {

  }

  public function isWhiteFlag () {

  }

  public function resolveAtack () {

  }

  public function resolveDefense () {

  }

  public function getAttackTroops () {
    return [];
  }

  public function getDefenseTroops () {
    return [];
  }

  public function loadFigthZone ($battle, $side) {
    $zone = [];
    $sizeUnits = 0;
    $sizeZone = $this->calcSizeFigthZone($battle);

    if ($side == 'attack') {
      $units = $this->getAttackTroops($battle);
    } else {
      $units = $this->getDefenseTroops($battle);
    }
    
    foreach ($units as $unit) {
      if ($sizeUnits < $sizeZone) {
        $zone = array_push($aZone, $unit);
      }
      $sizeUnits += $unit->size;
    }

    if ($side == 'attack') {
      $battle->attackZone = $zone;
    } else {
      $battle->defenseZone = $zone;
    }

    $battle->save();
    return $zone;
  }

  public function calcSizeFigthZone ($battle) {

  }

  public function getTravelers ($side, $planetId) {
    $travel = Travel::where('from', $planetId)->get();
    if ($travel) {
      return $travel;
    }
    return null;
  }

  public function checkEnd() {
    return true;
  }
}
