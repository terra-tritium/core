<?php

namespace App\Services;

use App\Models\Player;
use App\Models\Planet;
use App\Models\Effect;
use App\Models\AlianceRanking;
use App\Services\PlanetService;

class PlayerService
{
  protected $planetService;

  public function __construct () {
    $this->planetService = new PlanetService();
  }

  public function addBuildScore (Player $player, $units) {
    $player->buildScore += $units;
    return $player;
  }

  public function register(Player $player) {
    $player->score = 0;
    $player->buildScore = 0;
    $player->attackScore = 0;
    $player->defenseScore = 0;
    $player->militaryScore = 0;
    $player->researchScore = 0;
    $player->gameMode = 1;
    $player->attackStrategy = 1;
    $player->defenseStrategy = 1;
    $player->researchPoints = 0;
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
    $planet->workers = 30;
    $planet->workersWaiting = 30;
    $planet->workersOnMetal = 0;
    $planet->workersOnUranium = 0;
    $planet->workersOnCrystal = 0;
    $planet->workersOnLaboratory = 0;
    $planet->useEnergyByFactory = 0;
    $planet->status = "pacific";
    $planet->player = $player->id;
    $planet->metal = 1500;
    $planet->uranium = 0;
    $planet->crystal = 0;
    $planet->energy = 1;
    $planet->battery = 10000;
    $planet->extraBattery = 0;
    $planet->capMetal = 10000;
    $planet->capUranium = 10000;
    $planet->capCrystal = 10000;
    $planet->proMetal = 1000;
    $planet->proUranium = 1000;
    $planet->proCrystal = 1000;
    $planet->pwMetal = 0;
    $planet->pwUranium = 0;
    $planet->pwCrystal = 0;
    $planet->pwEnergy = 0;
    $planet->pwWorker = 0;
    $planet->transportShips = 0;
    $planet->researchPoints = 0;
    $planet->pwResearch = 0;
    $planet->save();

    $effect = new Effect();
    $effect->player = $player->id;
    $effect->speedProduceUnit = 0;
    $effect->speedProduceShip = 0;
    $effect->speedBuild = 0;
    $effect->speedResearch = 0;
    $effect->speedTravel = 0;
    $effect->speedMining = 0;
    $effect->costBuild = 0;
    $effect->protect = 0;
    $effect->extraAttack = 0;
    $effect->energyDiscount = 0;
    $effect->save();
  }

  private function startAlocation() {
    $coords = [];
    $lastPlanet = Planet::orderBy('id', 'desc')->first();

    if (!$lastPlanet) {
      return [
        'region' => 'A',
        'quadrant' => 'A000',
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

  public function getDetails($id) {
    $details = [];
    $player = Player::where('id', $id)->firstOrFail();
    $details['name'] = $player->name;
    $details['since'] = $player->since;
    $details['country'] = $player->country;
    $details['score'] = $player->score;
    if ($player->aliance != null) {
      $aliance = AlianceRanking::where('id', $player->aliance)->firstOrFail();
      $details['aliance'] = $aliance->name;
    } else {
      $details['aliance'] = "no aliance";
    }
    return $details;
  }

  public function iSplayerOwnerPlanet($player, $planet) {
    $planet = Planet::where(['player' => $player, 'id' => $planet])->first();
    if ($planet) {
      return true;
    }
    return false;
  }

  public function getPlanets() {
    $playerLogged = Player::getPlayerLogged();
    $planets = Planet::where('player', $playerLogged->id)->get();
    return $planets;
  }
}
