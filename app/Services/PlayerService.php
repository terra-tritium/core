<?php

namespace App\Services;

use App\Models\Player;
use App\Models\Planet;
use App\Models\Effect;
use App\Models\NFTConfig;
use App\Models\AlianceRanking;
use App\Models\Building;
use App\Models\Fleet;
use App\Models\Shipyard;
use App\Models\Troop;
use App\Services\PlanetService;
use Illuminate\Http\Response;

use function PHPUnit\Framework\isNull;

class PlayerService
{
  protected $planetService;

  public function __construct()
  {
    $this->planetService = new PlanetService();
  }

  public function addBuildScore(Player $player, $units)
  {
    $player->buildScore += $units;
    return $player;
  }

  public function register(Player $player)
  {
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

    // $lastPlanet = Planet::whereNull('player')->orderBy('id')->first();
    /**Pega o primeiro registro nulo que seja diferente de 1, o que esta na posição 1 é a estrela */
    $lastPlanet = Planet::whereNull('player')
      ->where('position', '<>', 1)
      ->orderBy('id')
      ->first();
    // $lastPlanet = Planet::orderByDesc('id')->first();

    if ($lastPlanet) {
      $lastPlanet->player = $player->id;
      $lastPlanet->defenseStrategy = 7;
      $lastPlanet->attackStrategy = 7;
      $lastPlanet->save();

      $effect = new Effect();
      $effect->player = $player->id;
      $effect->speedProduceUnit = 0;
      $effect->speedProduceShip = 0;
      $effect->speedBuild = 0;
      $effect->speedResearch = 0;
      $effect->speedTravel = 0;
      $effect->speedMining = 0;
      $effect->plasmaTechnology = 0;
      $effect->protect = 0;
      $effect->extraAttack = 0;
      $effect->discountEnergy = 0;
      $effect->discountBuild = 0;
      $effect->discountHumanoid = 0;
      $effect->save();

      return Response()->json(['message'=>'created'], Response::HTTP_CREATED);
    } else {
      return Response()->json(['message' => 'No available planet'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**Antigo trecho */
    return;
    $newAlocation = $this->startAlocation();

    $planet = new Planet();
    $planet->level = 1;
    $planet->name = "Colony";
    $planet->resource = $newAlocation['resource'];
    $planet->region = $newAlocation['region'];
    $planet->quadrant = $newAlocation['quadrant'];
    $planet->position = $newAlocation['position'];
    $planet->type = $newAlocation['type'];
    $planet->terrainType = $newAlocation['terrainType'];
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
    $planet->researchPoints = 0;
    $planet->pwResearch = 0;
    $planet->defenseStrategy = 7;
    $planet->attackStrategy = 7;
    $planet->save();
    // $this->createBuildings($planet->id);
    // $this->creatUnits($planet->id, $player->id);

    $effect = new Effect();
    $effect->player = $player->id;
    $effect->speedProduceUnit = 0;
    $effect->speedProduceShip = 0;
    $effect->speedBuild = 0;
    $effect->speedResearch = 0;
    $effect->speedTravel = 0;
    $effect->speedMining = 0;
    $effect->plasmaTechnology = 0;
    $effect->protect = 0;
    $effect->extraAttack = 0;
    $effect->discountEnergy = 0;
    $effect->discountBuild = 0;
    $effect->discountHumanoid = 0;
    $effect->save();

    $nftConfig = new NFTConfig();
    $nftConfig->player = $player->id;
    $nftConfig->slot1 = 0;
    $nftConfig->slot2 = 0;
    $nftConfig->slot3 = 0;
    $nftConfig->slot4 = 0;
    $nftConfig->slot5 = 0;
    $nftConfig->save();
  }
  private function creatUnits($planetId, $playerId)
  {
    for ($i = 0; $i <= rand(3, 8); $i++) {
      $troops = new Troop();
      $troops->player = $playerId;
      $troops->planet = $planetId;
      $troops->unit = rand(1, 12);
      $troops->quantity = rand(1, 7);
      $troops->save();
      $fleet = new Fleet();
      $fleet->player = $playerId;
      $fleet->planet = $planetId;
      $fleet->unit = rand(1, 12);
      $fleet->quantity = rand(1, 7);
      $fleet->save();
    }
  }

  private function creatShipyardUnits($planetId, $playerId)
  {
    for ($i = 0; $i <= rand(3, 8); $i++) {
      $shipyard = new Shipyard();
      $shipyard->player = $playerId;
      $shipyard->planet = $planetId;
      $shipyard->unit = rand(1, 12);
      $shipyard->quantity = rand(1, 7);
      $shipyard->save();
    }
  }
  /**
   * @TODO Remover função ao subir para produção
   */
  private function createBuildings($planetId)
  {
    $building = new Building();
    $building->build = 1;
    $building->planet = $planetId;
    $building->slot = 10;
    $building->level = 2;
    $building->workers = 1000;
    $building->ready = 1000;
    $building->save();

    // alianca
    $building = new Building();
    $building->build = 14;
    $building->planet = $planetId;
    $building->slot = 11;
    $building->level = 2;
    $building->workers = 1000;
    $building->ready = 1000;
    $building->save();
  }

  private function startAlocation()
  {
    $coords = [];
    $lastPlanet = Planet::orderBy('id', 'desc')->first();

    if (!$lastPlanet) {
      return [
        'region' => 'A',
        'quadrant' => 'A000',
        'position' => 1,
        'resource' => 'uranium',
        'type' => '1',
        'terrainType' => 'Desert'
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

    $terrainTypes = ["Desert", "Grass", "Ice", "Vulcan"];
    $terrainIndex = (ord($coords['region']) - ord('A')) % 4;
    $coords['terrainType'] = $terrainTypes[$terrainIndex];

    if ((ord($coords['region']) - ord('A')) % 8 < 4) {
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


  private function nextQuadrant($quadrant)
  {
    $nextLetter = substr($quadrant, 0, 1);
    $nextNumber = substr($quadrant, 1, 3) + 1;

    if ($nextNumber == 1000) {
      if ($nextLetter == "P") {
        $nextLetter = "A";
        $nextNumber = 0;
      } else {
        $nextLetter = chr(ord(substr($quadrant, 0, 1)) + 1);
        $nextNumber = 0;
      }
    }
    $next = $nextLetter . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    return $next;
  }


  private function coordInUse($coords)
  {
    $planet = Planet::where('quadrant', $coords['quadrant'])->where('position', $coords['position'])->first();
    if ($planet) {
      return true;
    } else {
      return false;
    }
  }

  public function getDetails($id,$playerId)
  {
    $id = $id == 0 || is_null($id) ? $playerId : $id;
    $details = [];
    $player = Player::where('id', $id)->firstOrFail();
    $details['name'] = $player->name;
    $details['since'] = $player->since;
    $details['country'] = $player->country;
    $details['score'] = $player->score;
    $details['transportShips'] = $player->transportShips;

    if ($player->aliance != null) {
      $aliance = AlianceRanking::where('id', $player->aliance)->firstOrFail();
      $details['aliance'] = $aliance->name;
    } else {
      $details['aliance'] = "no aliance";
    }
    return $details;
  }

  public function iSplayerOwnerPlanet($player, $planet)
  {
    $planet = Planet::where(['player' => $player, 'id' => $planet])->first();
    if ($planet) {
      return true;
    }
    return false;
  }

  public function getPlanets()
  {
    $playerLogged = Player::getPlayerLogged();
    $planets = Planet::where('player', $playerLogged->id)->get();
    return $planets;
  }

  public function changeName($player, $newName)
  {
    $playerModel = Player::find($player);
    $playerModel->name = $newName;
    $playerModel->save();

    return true;
  }
}
