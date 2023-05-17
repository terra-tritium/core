<?php

namespace App\Services;

use App\Models\Player;
use App\Models\Planet;
use App\Services\RankingService;

class PlayerService
{

  protected $timeNow;
  protected $rankingService;

  public function __construct () {
    $this->timeNow = time() * 1000;
    $this->rankingService = new RankingService();
  }

  public function currentBalance($p1, $type) {

    $msInHour = 3600000;
    $activeEnergyMining = ($this->timeNow - $p1->timeEnergy) / $msInHour;
    $activeMetalMining = ($this->timeNow - $p1->timeMetal) / $msInHour;
    $activeUraniumMining = ($this->timeNow - $p1->timeUranium) / $msInHour;
    $activeCrystalMining = ($this->timeNow - $p1->timeCrystal) / $msInHour;

    switch ($type) {
      case 0: 
        return $p1->energy + ($p1->pwEnergy * (env("TRITIUM_ENERGY") * $activeEnergyMining));
      case 1: 
        return $p1->metal + ($p1->pwMetal * (env("TRITIUM_METAL") * $activeMetalMining));
      case 2: 
        return $p1->uranium + ($p1->pwUranium * (env("TRITIUM_URANIUM") * $activeUraniumMining));
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

  public function startMining($player, $resource) {

    switch ($resource) {
      case 0:
        $player->timeEnergy = $this->timeNow;
        $player->pwEnergy = 1;
        break;

      case 1:
        $player->timeMetal = $this->timeNow;
        $player->pwMetal = 0;
        break;
    
      case 2:
        $player->timeUranium = $this->timeNow;
        $player->pwUranium = 0;
        break;

      case 3:
        $player->timeCrystal = $this->timeNow;
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
    $p1->timeMetal = $this->timeNow;
    $p1->metal += $units;
    return $p1;
  }

  public function removeMetal($p1, $units) {
    if ($this->currentBalance($p1, 1) > $units) {
      $p1->metal = $this->currentBalance($p1, 1);
      $p1->timeMetal = $this->timeNow;
      $p1->metal -= $units;
      $p1 = $this->rankingService->addPoints($p1, $units / 100);
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
      $p1 = $this->rankingService->addPoints($p1, $units / 20);
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
      $p1 = $this->rankingService->addPoints($p1, $units / 20);
    }
    return $p1;
  }

  public function register(Player $player) {
    $player->metal = 1500;
    $player->uranium = 0;
    $player->crystal = 0;
    $player->energy = 100;
    $player->battery = 10000;
    $player->extraBattery = 0;
    $player->capMetal = 10000;
    $player->capUranium = 10000;
    $player->capCrystal = 10000;
    $player->proMetal = 1000;
    $player->proUranium = 1000;
    $player->proCrystal = 1000;
    $player->pwMetal = 0;
    $player->pwUranium = 0;
    $player->pwCrystal = 0;
    $player->pwEnergy = 0;
    $player->merchantShips = 0;
    $player->score = 0;
    $player->buildScore = 0;
    $player->attackScore = 0;
    $player->defenseScore = 0;
    $player->militaryScore = 0;
    $player->researchScore = 0;
    $player->gameMode = 1;
    $player->attackStrategy = 1;
    $player->defenseStrategy = 1;
    $player->save();

    $newAlocation = $this->startAlocation();

    $planet = new Planet();
    $planet->level = 1;
    $planet->name = "Colony";
    $planet->resource = $newAlocation['resource'];
    $planet->region = $newAlocation['region'];
    $planet->quadrant = $newAlocation['quadrant'];
    $planet->position = $newAlocation['position'];
    $planet->type = $newAlocation['type'];
    $planet->player = $player->id;
    $planet->humanoids = 30;
    $planet->status = "pacific";
    $planet->player = $player->id;
    $planet->save();
  }

  public function addBuildScore ($p1, $units) {
    $p1->buildScore += $units;
    return $p1;
  }

  public function incrementBattery($p1, $units) {
    $p1->battery += $units;
    return $p1;
  }

  private function startAlocation() {
    $coords = [];
    $lastPlanet = Planet::orderBy('id', 'desc')->first();

    if (!$lastPlanet) {
      return [
        'region' => 'A',
        'quadrant' => 'A001',
        'position' => 1,
        'resource' => 'crystal',
        'type' => 1
      ];
    }

    $lastQuadrant = $lastPlanet->quadrant;
    $lastPosition = $lastPlanet->position;
    
    $cont = 0;

    do {
      $lastPosition += 1;
      if ($lastPosition <= 15) {
        $coords['quadrant'] = $lastQuadrant;
        $coords['position'] = $lastPosition;
      } else {
          $coords['quadrant'] = $this->nextQuadrant($lastQuadrant);
          $coords['position'] = 1;
      }
      $cont++;
    } while ($this->coordInUse($coords) == true && $cont < 1600);

    if ($cont > 1599) {
      return "end";
    }

    $coords['region'] = substr($coords['quadrant'], 0, 1);

    if ($coords['position'] % 2 == 0) {
      $coords['resource'] = "uranium";
    } else {
      $coords['resource'] = "crystal";
    }

    switch ($coords['position']) {
      case ($coords['position'] > 0 && $coords['position'] <= 4):
        $coords['type'] = 1;
        break;
      case ($coords['position'] >= 5 && $coords['position'] <= 8):
        $coords['type'] = 2;
        break;
      case ($coords['position'] >= 9 && $coords['position'] <= 12):
        $coords['type'] = 3;
        break;
      default:
        $coords['type'] = 4;
        break;
      }

    return $coords;
  }

  private function nextQuadrant($quadrant) {
    $nextLetter = substr($quadrant, 0, 1);
    $nextNumber = substr($quadrant, 2, 2) + 1;
    if ($nextNumber == 100) {
      if ($nextLetter == "P") {
        $nextLetter = "A";
        $nextNumber = 1;
      } else {
        $nextLetter = chr(ord(substr($quadrant, 0, 1)) + 1);
      }
    }
    $next =  $nextLetter . str_pad($nextNumber, 3 , '0' , STR_PAD_LEFT);
    return $next;
  }

  private function coordInUse($coords) {
    $planet = Planet::where('quadrant', $coords['quadrant'])->where('position', $coords['position'])->first();
    if ($planet) {
      return true;
    } else {
      return false;
    }
  }
}