<?php

namespace App\Services;

use App\Models\Planet;
use App\Models\Building;
use App\Models\Build;
use App\Models\Player;
use App\Services\RankingService;
use App\Services\ResearchService;

class PlanetService
{
  protected $timeNow;
  protected $rankingService;
  protected $pesoCaluleDistance;

  public function __construct () {
    $this->timeNow = time();
    $this->rankingService = new RankingService();
    $this->pesoCaluleDistance = config("app.tritium_travel_speed");
  }

  public function syncronizeEnergy(Planet $planet) {

    $level = 0;

    $levelEnergy = Building::where(['build' => Build::ENERGYCOLLECTOR, 'planet' => $planet->id])->first();

    if ($levelEnergy) {
      $level = $levelEnergy->level;
    }

    $energyMultiplier = $planet->terrainType ? $planet->terrainType->energy : 1.0;
    $currentBalance = $this->currentBalance($planet, 0, $level) * $energyMultiplier;
    $planet->energy = $currentBalance;
    $planet->timeEnergy = $this->timeNow;
    $planet->save();
  }

public function syncronizeDefenseScore(Planet $planet) {
  $defenseMultiplier = $planet->terrainType ? $planet->terrainType->defenseScore : 1.0;
  $planetDefense = $planet->baseDefense * $defenseMultiplier;
  $planet->defenseScore = $planetDefense;
  $planet->save();
}


public function currentBalance($p1, $type, $energyLevel = 1) {
  $effectService = new EffectService();
  $inHour = 3600;

  $activeEnergyMining =   ($this->timeNow - $p1->timeEnergy  ) / $inHour;
  $activeMetalMining =    ($this->timeNow - $p1->timeMetal   ) / $inHour;
  $activeUraniumMining =  ($this->timeNow - $p1->timeUranium ) / $inHour;
  $activeCrystalMining =  ($this->timeNow - $p1->timeCrystal ) / $inHour;
  $activeLaboratory =     ($this->timeNow - $p1->timeResearch) / $inHour;

  $workersOnEnergy = $p1->workersWaiting;

  if ($p1->workersWaiting > (config("app.tritium_energy_workers_by_level") * $energyLevel)) {
    $workersOnEnergy = config("app.tritium_energy_workers_by_level") * $energyLevel;
  }

    switch ($type) {
      case 0:
        return $p1->energy + ($workersOnEnergy * (config("app.tritium_energy_base") * $activeEnergyMining));
      case 1:
        return $p1->metal + ($p1->pwMetal * ($effectService->calcMiningSpeed(config("app.tritium_metal_base"), $p1)  * $activeMetalMining));
      case 2:
        // return $p1->uranium + ($p1->pwUranium * (env("TRITIUM_URANIUM_BASE") * $activeUraniumMining));
        return $p1->uranium + ($p1->pwUranium * ($effectService->calcMiningSpeed(config("app.tritium_metal_base"), $p1) * $activeUraniumMining));
      case 3:
        return $p1->crystal + ($p1->pwCrystal * ($effectService->calcMiningSpeed(config("app.tritium_metal_base"), $p1) * $activeCrystalMining));
        // return $p1->crystal + ($p1->pwCrystal * (env("TRITIUM_CRYSTAL_BASE") * $activeCrystalMining));
      case 4:
        return $p1->researchPoints + ($p1->pwResearch * ($effectService->calcResearchSpeed(config("app.tritium_research_speed"),$p1) * $activeLaboratory));
    }
    return 0;
}

  public function enoughBalance($p1, $units, $type, $energyLevel = 1) {
    if ($units == 0){
      return true;
    }
    switch ($type) {
      // Energia
      case 0:
        if ($this->currentBalance($p1, 0, $energyLevel) >= $units) {
          return true;
        }
        break;
      // Metal
      case 1:
        if ($this->currentBalance($p1, 1, $energyLevel) >= $units) {
          return true;
        }
        break;
      // Uranium
      case 2:
        if ($this->currentBalance($p1, 2, $energyLevel) >= $units) {
          return true;
        }
        break;
      // Cristal
      case 3:
        if ($this->currentBalance($p1, 3, $energyLevel) >= $units) {
          return true;
        }
        break;
    }

    return false;
  }

  public function startMining($planet, $resource) {

    switch ($resource) {
      case 0:
        $planet->timeEnergy = time();
        $planet->pwEnergy = 1;
        break;

      case 1:
        $planet->timeMetal = time();
        $planet->pwMetal = 0;
        break;

      case 2:
        $planet->timeUranium = time();
        $planet->pwUranium = 0;
        break;

      case 3:
        $planet->timeCrystal = time();
        $planet->pwCrystal = 0;
        break;
    }

    return $planet;
  }

  public function addEnergy($p1, $units) {
    $p1->energy += $units;
    return $p1;
  }

  public function removeEnergy($p1, $units) {
    $p1->energy -= $units;
    return $p1;
  }

  public function addMetal($p1, $units) {
    $p1->metal = $this->currentBalance($p1, 1);
    $p1->timeMetal = $this->timeNow;
    $p1->metal += $units;
    return $p1;
  }

  public function removeMetal($p1, $units) {
    if ($this->currentBalance($p1, 1) >= $units) {
      $p1->metal = $this->currentBalance($p1, 1);
      $p1->timeMetal = $this->timeNow;
      $p1->metal -= $units;
      $this->rankingService->addPoints($units / 100);
    }
    return $p1;
  }

  public function addUranium($p1, $units) {
    $p1->uranium = $this->currentBalance($p1, 2);
    $p1->timeUranium = $this->timeNow;
    $p1->uranium += $units;
    return $p1;
  }

  public function removeUranium($p1, $units) {
    if ($this->currentBalance($p1, 2) > $units) {
      $p1->uranium = $this->currentBalance($p1, 2);
      $p1->timeUranium = $this->timeNow;
      $p1->uranium -= $units;
      $this->rankingService->addPoints($units / 20);
    }
    return $p1;
  }

  public function addCrystal($p1, $units) {
    $p1->uranium = $this->currentBalance($p1, 3);
    $p1->timeUranium = $this->timeNow;
    $p1->uranium += $units;
    return $p1;
  }

  public function removeCrystal($p1, $units) {
    if ($this->currentBalance($p1, 2) > $units) {
      $p1->crystal = $this->currentBalance($p1, 3);
      $p1->timeCrystal = $this->timeNow;
      $p1->crystal -= $units;
      $this->rankingService->addPoints($units / 20);
    }
    return $p1;
  }

  public function incrementBattery(&$p1, $units) {
    $p1->battery += $units;
    return $p1;
  }

  public function calculeDistance($origin, $destiny, $peso = null) {
    $researchService = new ResearchService();

    $peso  = is_null($peso) ? $this->pesoCaluleDistance :  $peso ;

    $arrayRegion = ['A'=>0,'B'=>1,'C'=>2,'D'=>3,'E'=>4,'F'=>5,'G'=>6,'H'=>7,'I'=>8,'J'=>9,'K'=>10,'L'=>11,'M'=>12,'N'=>13,'O'=>14,'P'=>15];
    $result       = 0.5;
    $qtdQuadrante = 100;

    $originModel     = Planet::findOrFail($origin);
    $destinyModel    = Planet::findOrFail($destiny);

    $p1 = Player::find($originModel->player);
    $p2 = Player::find($destinyModel->player);

    $regionOrigin  = $originModel->region ;
    $regionDestiny =  $destinyModel->region ;

    $quatrandOrigin  =  $originModel->quadrant ;
    $quatrandDestiny =  $destinyModel->quadrant ;

    if( $regionOrigin != $regionDestiny){
      $regionsDistance =  abs($arrayRegion[$regionOrigin] - $arrayRegion[$regionDestiny]) ;
      $result  = ($regionsDistance  *  $qtdQuadrante);
    }else{
      if($quatrandOrigin != $quatrandDestiny){
        $q1 = substr($quatrandOrigin,1);
        $q2 =  substr( $quatrandDestiny,1);
        $result = abs($q1 - $q2 );
      }
    }

    if ($researchService->isResearched($originModel->player, 600) || $researchService->isResearched($destinyModel->player, 600)) {
      $peso -= 180;
    }

    return $result * $peso;
  }

  public function onFire($planetId) {
    $planet = Planet::findOrFail($planetId);
    $planet->onFire = true;
    $planet->save();
  }

  public function offFire($planetId) {
    $planet = Planet::findOrFail($planetId);
    $planet->onFire = false;
    $planet->save();
  }

}
