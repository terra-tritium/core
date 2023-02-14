<?php

namespace App\Services;

use App\Models\Player;

use Carbon\Carbon;

class PlayerService
{

  public function currentBalance($p1, $type) {

    $msInHour = 3600000;
    $activeEnergyMining = (time() - $p1->timeEnergy) / $msInHour;
    $activeMetalMining = (time() - $p1->timeMetal) / $msInHour;
    $activeDeuteriumMining = (time() - $p1->timeDeuterium) / $msInHour;
    $activeCrystalMining = (time() - $p1->timeCrystal) / $msInHour;

    switch ($type) {
      case 0: 
        return $p1->energy + ($p1->pwEnergy * (env("TRITIUM_ENERGY") * $activeEnergyMining));
      case 1: 
        return $p1->metal + ($p1->pwMetal * (env("TRITIUM_METAL") * $activeMetalMining));
      case 2: 
        return $p1->deuterium + ($p1->pwDeuterium * (env("TRITIUM_DEUTERIUM") * $activeDeuteriumMining));
      case 3:
        return $p1->crystal + ($p1->pwCrysttal * (env("TRITIUM_Crystal") * $activeCrystalMining));
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
        if ($this.currentBalance($p1, 1) >= $units) {
          return true;
        }
        break;
      case 2:
        if ($this.currentBalance($p1, 2) >= $units) {
          return true;
        }
        break;
      case 3:
        if ($this.currentBalance($p1, 3) >= $units) {
          return true;
        }
        break;
    }

    return false;
  }

  public function startMining($player, $resource) {

    switch ($resource) {
      case 0:
        $player->timeEnergy = time();
        $player->onEnergy = true;
        $player->pwEnergy = 1;
        break;

      case 1:
        $player->timeMetal = time();
        $player->onMetal = true;
        $player->pwMetal = 0;
        break;
    
      case 2:
        $player->timeDeuterium = time();
        $player->onDeuterium = true;
        $player->pwDeuterium = 0;
        break;

      case 3:
        $player->timeCrystal = time();
        $player->onCrystal = true;
        $player->pwCrystal = 0;
        break;
    }

    return $player;
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
    $p1->timeMetal = time();
    $p1->metal += $units;
    return $p1;
  }

  public function removeMetal($p1, $units) {
    if ($this.currentBalance($p1, 1) > $units) {
      $p1->metal = $this->currentBalance($p1, 1);
      $p1->timeMetal = time();
      $p1->metal -= $units;
    }
    return $p1;
  }

  public function addDeuterium($p1, $units) {
    $p1->deuterium = this.currentBalance($p1, 2);
    $p1->timeDeuterium = time();
    $p1->deuterium += $units;
    return $p1;
  }

  public function removeDeuterium($p1, $units) {
    if (this.currentBalance(p1, 2) > $units) {
      $p1->deuterium = this.currentBalance(p1, 2);
      $p1->timeDeuterium = time();
      $p1->deuterium -= units;
    }
    return $p1;
  }

  public function addCrystal($p1, $units) {
    $p1->deuterium = this.currentBalance(p1, 2);
    $p1->timeDeuterium = time();
    $p1->deuterium += $units;
    return $p1;
  }

  public function removeCrystal(Player $p1, long $units) {
    if (this.currentBalance(p1, 2) > $units) {
      $p1->crystal = $this.currentBalance(p1, 2);
      $p1->timeCrystal = time();
      $p1->crystal -= $units;
    }
    return $p1;
  }

  public function register(Player $player) {
    //$player->since = Carbon::now()->timestamp;
    $player->metal = 0;
    $player->deuterium = 0;
    $player->crystal = 0;
    $player->energy = 0;
    $player->pwMetal = 0;
    $player->pwDeuterium = 0;
    $player->pwCrystal = 0;
    $player->pwEnergy = 0;
    $player->battery = 0;
    $player->merchantShips = 0;
    $player->score = 0;
    $player->buildScore = 0;
    $player->labScore = 0;
    $player->tradeScore = 0;
    $player->attackScore = 0;
    $player->defenseScore = 0;
    $player->warScore = 0;
    $player->save();
  }


}