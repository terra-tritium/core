<?php

namespace App\Services;

use App\Jobs\LandCombatJob;
use App\Models\Combat;
use App\Models\Travel;
use App\Models\Planet;

class LandCombatService
{
  public function start ($planetId, $travelId) {
    $combat = new Combat([
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
    $combat->save();
    $travel = Travel::find($travelId);
    $planet = Planet::find($planetId);
    $this->loadAttackTroops($travel, $combat);
    $this->loadDefenseTroops(null, $combat);
    $this->startStage($combat);
  }

  public function join ($side, $combat, $travler) {
    if ($side == 'attack') {
      $this->loadAttackTroops($travler, $combat);
    } else {
      $this->loadDefenseTroops($travler, $combat);
    }
  }

  public function loadAttackTroops ($travel, $combat) {
    $combat->addAttackUnits($travel->troop);
    $combat->save();
  }

  public function loadDefenseTroops ($travel, $combat) {
    if ($travel == null) {
      $planet = Planet::find($combat->planet);
      $combat->addDefenseUnits($planet->troops);
    } else {
      $combat->addDefenseUnits($travel->troop);
    }
    $combat->save();
  }

  public function startStage ($combat) {
    LandCombatJob::dispatch(
      $this,
      $combat
    )->delay(now()->addSeconds(5));
  }

  public function runStage($combatId, $side) {
    $combat = Combat::find($combatId);
    $aForce = $this->calcAttackForce($side);
    $aForce += $this->applyAttackEffects($side);

    $dForce = $this->calcDefenseForce($side);
    $dForce += $this->applyDefenseEffects($side);

    $aForce += $this->resolveStrategy('attack');
    $dForce += $this->resolveStrategy('defense');

    $demage = $this->calcDemage($aForce, $dForce);

    $shielAffected = $this->hitShield($demage);
    $demage = $demage - $shielAffected;
    $this->hitTroops($combat, $side, $demage);

    if ($side == 'defense' && !$this->checkEnd()) {
      LandCombatJob::dispatch(
          $this,
          $combatId
      )->delay(now()->addSeconds(config("app.tritium_stage_speed")));
    }

    # recebe novos viajantes que chegaram ao planeta
    $travels = $this->getTravelers($side, $combat->planet);
    if ($travels) {
      foreach ($travels as $member) {
        $this->join($side, $combat, $member);
      }
    }
  }

  public function calcAttackForce ($combat) {
    $force = 0;
    $aZone = $this->loadFigthZone($combat, 'attack');
    foreach ($aZone as $unit) {
      $force += $unit->attack;
    }
    return $force;
  }

  public function calcDefenseForce ($combat) {
    $force = 0;
    $aZone = $this->loadFigthZone($combat, 'defense');
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

  public function loadFigthZone ($combat, $side) {
    $zone = [];
    $sizeUnits = 0;
    $sizeZone = $this->calcSizeFigthZone($combat);

    if ($side == 'attack') {
      $units = $this->getAttackTroops($combat);
    } else {
      $units = $this->getDefenseTroops($combat);
    }
    
    foreach ($units as $unit) {
      if ($sizeUnits < $sizeZone) {
        $zone = array_push($aZone, $unit);
      }
      $sizeUnits += $unit->size;
    }

    if ($side == 'attack') {
      $combat->attackZone = $zone;
    } else {
      $combat->defenseZone = $zone;
    }

    $combat->save();
    return $zone;
  }

  public function calcSizeFigthZone ($combat) {

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
