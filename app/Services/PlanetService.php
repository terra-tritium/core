<?php

namespace App\Services;

use App\Models\Planet;
use App\Services\RankingService;

class PlanetService
{
  protected $timeNow;
  protected $rankingService;

  public function __construct () {
    $this->timeNow = time();
    $this->rankingService = new RankingService();
  }

  public function syncronizeEnergy(Planet $planet) {
    $currentBalance = $this->currentBalance($planet, 0);
    $planet->energy = $currentBalance;
    $planet->timeEnergy = $this->timeNow;
    $planet->save();
  }

  public function currentBalance($p1, $type) {
    # secounds in hour
    $sInHour = 3600;

    $activeEnergyMining = ($this->timeNow - $p1->timeEnergy) / $sInHour;
    $activeMetalMining = ($this->timeNow - $p1->timeMetal) / $sInHour;
    $activeUraniumMining = ($this->timeNow - $p1->timeUranium) / $sInHour;
    $activeCrystalMining = ($this->timeNow - $p1->timeCrystal) / $sInHour;
    $activeLaboratory = ($this->timeNow - $p1->timeResearch) / $sInHour;

    switch ($type) {
      case 0:
        return $p1->energy + ($p1->workersWaiting * (env("TRITIUM_ENERGY") * $activeEnergyMining));
      case 1:
        return $p1->metal + ($p1->pwMetal * (env("TRITIUM_METAL") * $activeMetalMining));
      case 2:
        return $p1->uranium + ($p1->pwUranium * (env("TRITIUM_URANIUM") * $activeUraniumMining));
      case 3:
        return $p1->crystal + ($p1->pwCrystal * (env("TRITIUM_CRYSTAL") * $activeCrystalMining));
      case 4:
        return $p1->researchPoints + ($p1->pwResearch * (env("TRITIUM_RESEARCH_SPEED") * $activeLaboratory));
    }

    return 0;
  }

  public function enoughBalance($p1, $units, $type) {
    if ($units == 0){
      return true;
    }
    switch ($type) {
      case 0:
        if ($this->currentBalance($p1, 0) >= $units) {
          return true;
        }
        break;
      case 1:
        if ($this->currentBalance($p1, 1) >= $units) {
          return true;
        }
        break;
      case 2:
        if ($this->currentBalance($p1, 2) >= $units) {
          return true;
        }
        break;
      case 3:
        if ($this->currentBalance($p1, 3) >= $units) {
          return true;
        }
        break;
    }

    return false;
  }

  public function startMining($planet, $resource) {

    switch ($resource) {
      case 0:
        $planet->timeEnergy = $this->timeNow;
        $planet->pwEnergy = 1;
        break;

      case 1:
        $planet->timeMetal = $this->timeNow;
        $planet->pwMetal = 0;
        break;

      case 2:
        $planet->timeUranium = $this->timeNow;
        $planet->pwUranium = 0;
        break;

      case 3:
        $planet->timeCrystal = $this->timeNow;
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
    if ($this->currentBalance($p1, 1) > $units) {
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
    $p1->uranium = $this->currentBalance($p1, 2);
    $p1->timeUranium = $this->timeNow;
    $p1->uranium += $units;
    return $p1;
  }

  public function removeCrystal($p1, $units) {
    if ($this->currentBalance($p1, 2) > $units) {
      $p1->crystal = $this->currentBalance($p1, 2);
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

}
