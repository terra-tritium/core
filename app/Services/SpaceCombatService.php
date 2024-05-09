<?php

namespace App\Services;

use App\Models\Building;
use App\Models\Build;
use App\Models\Combat;
use App\Models\CombatStage;
use App\Models\Fighters;
use App\Models\Planet;
use App\Models\Player;
use App\Models\Ship;
use App\Models\Fleet;
use App\Models\Strategy;
use App\Models\Travel;
use App\Services\PlanetService;
use App\Services\PlayerService;
use App\Services\LogService;
use App\Jobs\SpaceCombatJob;
use App\Jobs\TravelJob;

class SpaceCombatService
{
  private $battleFieldSize;
  private $randStart = 0;
  private $randEnd = 5;
  private $totalKillInvasor = 0;
  private $totalKilllocal = 0;
  private $totalDemageInvasor = 0;
  private $totalDemageLocal = 0;
  private $totalLocalShips = 0;
  private $totalInvasorShips = 0;

  public function __construct() {
    $this->battleFieldSize = 10;
  }

  public function createNewCombat ($travel) {

    $combat = new Combat();
    $combat->planet = $travel->to;
    $combat->status = Combat::STATUS_CREATE;
    $combat->start = time();
    $combat->stage = 0;
    $combat->save();

    $player1 = new Fighters();
    $player1->combat = $combat->id;
    $player1->player = $travel->player;
    $player1->side = Combat::SIDE_INVASOR;
    $player1->strategy = $travel->strategy;
    $player1->demage = 0;
    $player1->start = time();
    $player1->stage = 0;
    $player1->planet = $travel->from;
    $player1->transportShips = $travel->transportShips;
    $player1->cruiser = $travel->cruiser;
    $player1->craft = $travel->craft;
    $player1->bomber = $travel->bomber;
    $player1->scout = $travel->scout;
    $player1->stealth = $travel->stealth;
    $player1->flagship = $travel->flagship;
    $player1->save();

    $planet = Planet::find($travel->to);

    $player2 = new Fighters();
    $player2->combat = $combat->id;
    $player2->player = $planet->player;
    $player2->side = Combat::SIDE_LOCAL;
    $player2->strategy = $planet->defenseStrategy;
    $player2->demage = 0;
    $player2->start = time();
    $player2->stage = 0;
    $player2->planet = $travel->to;
    $player2->transportShips = $travel->transportShips;
    $player2->cruiser = 0;
    $player2->craft = 0;
    $player2->bomber = 0;
    $player2->scout = 0;
    $player2->stealth = 0;
    $player2->flagship = 0;

    $fleet = Fleet::where('planet', $travel->to)->get();

    if ($fleet) {
      foreach ($fleet as $ship) {
        switch ($ship->unit) {
          case Ship::SHIP_CRUISER:
            $player2->cruiser = $ship->quantity;
            break;
          case Ship::SHIP_CRAFT:
            $player2->craft = $ship->quantity;
            break;
          case Ship::SHIP_BOMBER:
            $player2->bomber = $ship->quantity;
            break;
          case Ship::SHIP_SCOUT:
            $player2->scout = $ship->quantity;
            break;
          case Ship::SHIP_STEALTH:
            $player2->stealth = $ship->quantity;
            break;
          case Ship::SHIP_FLAGSHIP:
            $player2->flagship = $ship->quantity;
            break;
        }
      }
    }

    $player2->save();

    $this->totalLocalShips = $player2->cruiser + $player2->craft + $player2->bomber + $player2->scout + $player2->stealth + $player2->flagship;
    $this->totalInvasorShips = $player1->cruiser + $player1->craft + $player1->bomber + $player1->scout + $player1->stealth + $player1->flagship;

    $this->ajustBatleField();

    $this->startCombat($combat->id);
  }

  private function ajustBatleField() {
    $minorShips = 0;
    if ($this->totalLocalShips < $this->totalInvasorShips) {
      $minorShips = $this->totalLocalShips;
    } else {
      $minorShips = $this->totalInvasorShips;
    }
    switch ($minorShips) {
      case $minorShips < 50;
        $this->battleFieldSize = 10;
        break;
      case $minorShips < 100;
        $this->battleFieldSize = 20;
        break;
      case $minorShips < 200;
        $this->battleFieldSize = 30;
        break;
      case $minorShips < 300;
        $this->battleFieldSize = 40;
        break;
      case $minorShips < 400;
        $this->battleFieldSize = 50;
        break;
      case $minorShips < 1000;
        $this->battleFieldSize = 60;
        break;
      case $minorShips > 2000;
        $this->battleFieldSize = 100;
        break;
    }
  }

  public function startCombat($combatId) {
    $combat = Combat::find($combatId);

    if ($combat->status != Combat::STATUS_CREATE) {
      return false;
    }

    $combat->status = Combat::STATUS_RUNNING;
    $combat->stage = 1;
    $combat->nextStage = time() + config("app.tritium_combat_stage_time");
    $combat->save();

    # Queue next stage
    SpaceCombatJob::dispatch($combatId)->delay(now()->addSeconds( config("app.tritium_combat_stage_time")));
  }

  public function excuteStage($combatId) {
    $combat = Combat::find($combatId);

    if ($combat->status == Combat::STATUS_CREATE) {
      $this->startCombat($combatId);
    }

    $invasors = Fighters::where(['combat'=>$combatId, 'side'=>Combat::SIDE_INVASOR])->get();
    $locals = Fighters::where(['combat'=>$combatId, 'side'=>Combat::SIDE_LOCAL])->get();

    # Locals no more players
    if ($locals->count() == 0) {
      $this->finishCombat($combatId, Combat::SIDE_INVASOR);
      return true;
    }

    # Invasors no more players
    if ($invasors->count() == 0) {
      $this->finishCombat($combatId, Combat::SIDE_LOCAL);
      return true;
    }

    if ($this->haveShips($locals)) {
      $this->resolve($combat, $invasors, $locals);
    } else {
      # Invasors win
      $this->finishCombat($combatId, Combat::SIDE_INVASOR);
      return true;
    }

    if ($this->haveShips($invasors)) {
      $this->resolve($combat, $locals, $invasors);
    } else {
      # Locals win
      $this->finishCombat($combatId, Combat::SIDE_LOCAL);
      return true;
    }

    $playerService = new PlayerService();

    # Apply scores
    foreach ($locals as $local) {
        $damagePerLocal = max(0, $this->totalDemageInvasor / floor(count($locals)));
        $playerService->addAttackScore($local->player, $damagePerLocal);
    }

    foreach ($invasors as $invasor) {
        $damagePerInvasor = max(0, $this->totalDemageLocal / floor(count($invasors)));
        $playerService->addAttackScore($invasor->player, $damagePerInvasor);
    }

    # Log stage informations
    $this->logStage(
      $combat,
      "Invasor Kills: " . $this->totalKillInvasor . " / Locals Kills: " . $this->totalKilllocal,
      $this->totalKillInvasor,
      $this->totalKilllocal,
      $this->totalDemageInvasor,
      $this->totalDemageLocal
    );

    $combat->stage++;
    $combat->nextStage = time() + config("app.tritium_combat_stage_time");
    $combat->save();

    # Queue next stage
    SpaceCombatJob::dispatch($combatId)->delay(now()->addSeconds( config("app.tritium_combat_stage_time")));
  }

  private function logStage($combat, $message, $killInvasor = 0, $killLocal = 0, $demageInvasor = 0, $demageLocal = 0) {
    $cs = new CombatStage();
    $cs->combat = $combat->id;
    $cs->message = $message;
    $cs->number = $combat->stage;
    $cs->killInvasor = $killInvasor;
    $cs->killLocal = $killLocal;
    $cs->demageInvasor = $demageInvasor;
    $cs->demageLocal = $demageLocal;
    $cs->save();
  }

  public function leave($combatId, $player) {
    $combat = Combat::find($combatId);

    $planetService = new PlanetService();
    $now = time();
    $figther = Fighters::where(['combat'=>$combatId, 'player'=>$player->id])->first();

    if (!$figther) {
      return false;
    }

    if (!$this->validReturn($player->id)) {
      return false;
    }

    $travel = new Travel();
    $travel->player = $player->id;
    $travel->receptor = $player->id;
    $travel->from = $combat->planet;
    $travel->to = $figther->planet;
    $travel->action = Travel::RETURN_FLEET;
    $travel->transportShips = $figther->transportShips;
    $travel->cruiser = $figther->cruiser;
    $travel->craft = $figther->craft;
    $travel->bomber = $figther->bomber;
    $travel->scout = $figther->scout;
    $travel->stealth = $figther->stealth;
    $travel->flagship = $figther->flagship;
    $travel->start = $now;
    $travelTime = $planetService->calculeDistance($travel->from, $travel->to);
    $travel->arrival = $now + $travelTime;
    $travel->status = Travel::STATUS_ON_GOING;
    $travel->save();

    TravelJob::dispatch($this, $travel->id, false)->delay(now()->addSeconds($travelTime));

    if ($figther) {
      $figther->delete();
    }
  }

  private function validReturn ($player) {
    $travelAtack = Travel::where([['player', $player], ['action', Travel::ATTACK_FLEET]])->orderBy('id', 'desc')->first();
    $travelReturn = Travel::where([['player', $player], ['action', Travel::RETURN_FLEET]])->orderBy('id', 'desc')->first();
    if ($travelAtack) {
        if (!$travelReturn) {
            return true;
        }
        if ($travelAtack->id > $travelReturn->id) {
            return true;
        }
    }
  }

  private function finishCombat($combatId, $winner) {
    $combat = Combat::find($combatId);
    $combat->status = Combat::STATUS_FINISH;
    $combat->winner = $winner;
    $combat->save();
    $this->logStage($combat, 'Combat finish, winner: ' . $winner);

    if ($winner == Combat::SIDE_INVASOR) {
        $stolen = $this->pillage($combat, Fighters::where(['combat'=>$combatId, 'side'=>Combat::SIDE_INVASOR])->get());
        $this->logStage($combat, 'Total stolen: ' . $stolen . ' resources');
    }

    $fighters = Fighters::where('combat', $combatId)->get();
    foreach ($fighters as $fighter) {
        if ($fighter->side == $winner) {
            $this->initiateReturn($combatId, $fighter, true);
        } else {
            $this->initiateReturn($combatId, $fighter, false, true);
        }
    }
}

private function initiateReturn($combatId, $fighter, $isWinner, $isLoser = false) {
    $combat = Combat::find($combatId);
    if (!$combat) return false;

    $planetService = new PlanetService();
    $now = time();
    $resources = $isWinner ? ['metal' => $fighter->metal, 'crystal' => $fighter->crystal, 'uranium' => $fighter->uranium] : ['metal' => 0, 'crystal' => 0, 'uranium' => 0];

    $travel = new Travel();
    $travel->player = $fighter->player;
    $travel->receptor = $fighter->player;
    $travel->from = $combat->planet;
    $travel->to = $fighter->planet;
    $travel->action = Travel::RETURN_FLEET;
    $travel->transportShips = $fighter->transportShips;
    $travelTime = $planetService->calculeDistance($travel->from, $travel->to);
    $travel->metal = $resources['metal'];
    $travel->crystal = $resources['crystal'];
    $travel->uranium = $resources['uranium'];
    $travel->status = Travel::STATUS_ON_GOING;

    if ($isLoser) {
        $travel->cruiser = 0;
        $travel->craft = 0;
        $travel->bomber = 0;
        $travel->scout = 0;
        $travel->stealth = 0;
        $travel->flagship = 0;
        $travel->metal = 0;
        $travel->crystal = 0;
        $travel->uranium = 0;
    } else {
        $travel->cruiser = $fighter->cruiser;
        $travel->craft = $fighter->craft;
        $travel->bomber = $fighter->bomber;
        $travel->scout = $fighter->scout;
        $travel->stealth = $fighter->stealth;
        $travel->flagship = $fighter->flagship;
    }

    $travel->save();
    TravelJob::dispatch($this, $travel->id, false)->delay(now()->addSeconds($travelTime));
}

  private function haveShips($fighters) {
    $ships = 0;
    foreach ($fighters as $figther) {
      $ships += $figther->cruiser + $figther->craft + $figther->bomber + $figther->scout + $figther->stealth + $figther->flagship;
    }
    return $ships > 0;
  }

  private function getDemageEffects($combat, $p1StrategyId, $p2StrategyId, $planetOwnner = false) {
    $p1Strategy = Strategy::find($p1StrategyId);
    $p2Strategy = Strategy::find($p2StrategyId);

    if ($planetOwnner) {
      $shieldForce = $this->getShieldForce($combat->planet);
      $this->logStage($combat, 'Shield activate: ' . $shieldForce . ' force');
    } else {
      $shieldForce = 0;
    }

    if ($p1Strategy && $p2Strategy) {
      $effects = $p1Strategy->attack - ($p2Strategy->defense + $shieldForce);
    }

    $this->logStage($combat, $p1Strategy->name . ' effect: ' . $effects . ' demage');

    return $effects;
  }

  private function applyDemage($demage, $figther, $hp, $qtdPlayers, $effects, $ship) {
    $demage = max(0, $demage + $effects);
    $kills = $demage / $hp;
    $kills = ceil($kills / $qtdPlayers);

    $kills = max(0, $kills);

    if ($figther->side == Combat::SIDE_LOCAL) {
      $this->totalDemageLocal += $demage;
      $this->totalKilllocal += $kills;
    } else {
      $this->totalDemageInvasor += $demage;
      $this->totalKillInvasor += $kills;
    }

    if ($figther->$ship > 0) {
        $newQuantity = $figther->$ship - $kills;
        if ($newQuantity < 0) {
            $newQuantity = 0;
        }

        $figther->$ship = $newQuantity;
    }
    return $figther;
}


  private function pillage($combat, $invasors) {
    $planet = Planet::find($combat->planet);
    $buildWhareHouse    = 9;
    $metalProtected     = 0 ;
    $crystalProtected   = 0;
    $uraniumProtected   = 0;

    $mdWhareHouse = Building::where(['planet'=> $combat->planet,'build' => $buildWhareHouse])->first();

    if(!is_null($mdWhareHouse))
    {
        $metalProtected     = $planet->capMetal     * $mdWhareHouse->level ;
        $crystalProtected   = $planet->capCrystal   * $mdWhareHouse->level;
        $uraniumProtected   = $planet->capUranium   * $mdWhareHouse->level;

        $planet->metal   -= $metalProtected;
        $planet->crystal -= $crystalProtected;
        $planet->uranium -= $uraniumProtected;
    }

    $planetService = new PlanetService();
    $stolen = 0;
    foreach ($invasors as $invasor) {
      $capacity = $invasor->transportShips * config("app.tritium_transportship_capacity");
      $metal = 0;
      $crystal = 0;
      $uranium = 0;

      if ($planet->metal >= $capacity && $planet->metal  > 0) {
        $planet->metal -= $capacity;
        $stolen += $capacity;
        $metal = $capacity;
        $capacity = 0;
      } elseif($planet->metal  > 0) {
        $stolen += $planet->metal;
        $metal = $planet->metal;
        $capacity -= $planet->metal;
        $planet->metal = 0;
      }

      if ($planet->crystal >= $capacity && $planet->crystal  > 0) {
        $planet->crystal -= $capacity;
        $stolen += $capacity;
        $crystal = $capacity;
        $capacity = 0;
      } elseif($planet->crystal  > 0) {
        $stolen += $planet->crystal;
        $crystal = $planet->crystal;
        $capacity -= $planet->crystal;
        $planet->crystal = 0;
      }

      if ($planet->uranium >= $capacity && $planet->uranium  > 0) {
        $planet->uranium -= $capacity;
        $stolen += $capacity;
        $uranium = $capacity;
        $capacity = 0;
      } elseif($planet->uranium  > 0) {
        $stolen += $planet->uranium;
        $uranium = $planet->uranium;
        $capacity -= $planet->uranium;
        $planet->uranium = 0;
      }

      $now = time();
      $travel = new Travel();
      $travel->player = $invasor->player;
      $travel->receptor = $invasor->player;
      $travel->from = $combat->planet;
      $travel->to = $invasor->planet;
      $travel->action = Travel::RETURN_FLEET;
      $travel->transportShips = $invasor->transportShips;
      $travel->cruiser = $invasor->cruiser;
      $travel->craft = $invasor->craft;
      $travel->bomber = $invasor->bomber;
      $travel->scout = $invasor->scout;
      $travel->stealth = $invasor->stealth;
      $travel->flagship = $invasor->flagship;
      $travel->start = $now;
      $travelTime = $planetService->calculeDistance($travel->from, $travel->to);
      $travel->arrival = $now + $travelTime;
      $travel->status = Travel::STATUS_ON_GOING;
      $travel->metal = $metal;
      $travel->crystal = $crystal;
      $travel->uranium = $uranium;
      $travel->save();

      TravelJob::dispatch($this, $travel->id, false)->delay(now()->addSeconds($travelTime));
    }

    $planet->metal   += $metalProtected;
    $planet->crystal += $crystalProtected;
    $planet->uranium += $uraniumProtected;

    $planet->save();

    # Log local pillage
    $logService = new LogService();
    $logService->notify(
      $planet->player,
      "After the combat some of its resources were looted, "
      . $travel->metal . " metal, "
      . $travel->crystal . " crystal and "
      . $travel->uranium . " uranium",
      "Space Combat"
    );

    return $stolen;
  }

  public function landingOfShips($travel) {
    $planetService = new PlanetService();
    $planetService->offFire($travel->to);
    $planetService->offFire($travel->from);
    $this->addFleet($travel);
    $this->addTransportShips($travel);
    $this->depositeResource($travel);
  }

  public function addFleet($travel){
    $this->addShip($travel, Ship::SHIP_CRAFT, $travel->craft);
    $this->addShip($travel, Ship::SHIP_BOMBER, $travel->bomber);
    $this->addShip($travel, Ship::SHIP_CRUISER, $travel->cruiser);
    $this->addShip($travel, Ship::SHIP_SCOUT, $travel->scout);
    $this->addShip($travel, Ship::SHIP_STEALTH, $travel->stealth);
    $this->addShip($travel, Ship::SHIP_FLAGSHIP, $travel->flagship);
  }

  public function addTransportShips($travel) {
    $player = Player::find($travel->player);
    $player->transportShips += $travel->transportShips;
    $player->save();
  }

  private function addShip($travel, $shipCode, $qtdShips) {
    $fleet = Fleet::where([
        'unit'      => $shipCode,
        'player'    => $travel->player,
        'planet'    => $travel->to
    ])->first();

    if ($fleet) {
      $fleet->quantity = ($fleet->quantity + $qtdShips);
      $fleet->save();
    }
  }

  private function depositeResource ($travel) {
      $planet = Planet::where('id', $travel->to)->first();
      $planet->metal += $travel->metal;
      $planet->crystal += $travel->crystal;
      $planet->uranium += $travel->uranium;
      $planet->save();

      $logService = new LogService();
      $logService->notify(
        $travel->player,
        "His fleet returned from combat and brought, "
        . $travel->metal . " metal, "
        . $travel->crystal . " crystal and "
        . $travel->uranium . " uranium",
        "Space Combat"
      );
  }

  private function sincronizeFleet($planet, $fighter) {
    $fleet = Fleet::where('planet', $planet->id)->get();

    foreach ($fleet as $ship) {
      switch ($ship->unit) {
        case Ship::SHIP_CRUISER:
          $ship->quantity = $fighter->cruiser;
          break;
        case Ship::SHIP_CRAFT:
          $ship->quantity = $fighter->craft;
          break;
        case Ship::SHIP_BOMBER:
          $ship->quantity = $fighter->bomber;
          break;
        case Ship::SHIP_SCOUT:
          $ship->quantity = $fighter->scout;
          break;
        case Ship::SHIP_STEALTH:
          $ship->quantity = $fighter->stealth;
          break;
        case Ship::SHIP_FLAGSHIP:
          $ship->quantity = $fighter->flagship;
          break;
      }
      $ship->save();
    }
  }

  private function getShieldForce($planetId) {
    $shieldBuild = Building::where([['build', Build::SHIELD],['planet', $planetId]])->first();
    if ($shieldBuild) {
      return $shieldBuild->level * config("app.tritium_shield_force");
    }
    return 0;
  }

  private function resolve($combat, $invasors, $locals) {

    $invasorCraftAttack = 0;
    $localCraftAttack = 0;
    $invasorCraftDefense = 0;
    $localCraftDefense = 0;

    $invasorBomberAttack = 0;
    $localBomberAttack = 0;
    $invasorBomberDefense = 0;
    $localBomberDefense = 0;

    $invasorCruiserAttack = 0;
    $localCruiserAttack = 0;
    $invasorCruiserDefense = 0;
    $localCruiserDefense = 0;

    $invasorScoutAttack = 0;
    $localScoutAttack = 0;
    $invasorScoutDefense = 0;
    $localScoutDefense = 0;

    $invasorStealthAttack = 0;
    $localStealthAttack = 0;
    $invasorStealthDefense = 0;
    $localStealthDefense = 0;

    $invasorFlagshipAttack = 0;
    $localFlagshipAttack = 0;
    $invasorFlagshipDefense = 0;
    $localFlagshipDefense = 0;

    foreach ($invasors as $invasor) {

      # Adjust ships to battle field size
      $invasorCraftInBattle = $invasor->craft > $this->battleFieldSize ? $this->battleFieldSize : $invasor->craft;
      $invasorBomberInBattle = $invasor->bomber > $this->battleFieldSize ? $this->battleFieldSize : $invasor->bomber;
      $invasorCruiserInBattle = $invasor->cruiser > $this->battleFieldSize ? $this->battleFieldSize : $invasor->cruiser;
      $invasorScoutInBattle = $invasor->scout > $this->battleFieldSize ? $this->battleFieldSize : $invasor->scout;
      $invasorStealthInBattle = $invasor->stealth > $this->battleFieldSize ? $this->battleFieldSize : $invasor->stealth;
      $invasorFlagshipInBattle = $invasor->flagship > $this->battleFieldSize ? $this->battleFieldSize : $invasor->flagship;

      # Calculate attack and defense
      $invasorCraftAttack     += ($invasorCraftInBattle    * Ship::SHIP_CRAFT_ATTACK     ) + rand($this->randStart, $this->randEnd);
      $invasorCraftDefense    += ($invasorCraftInBattle    * Ship::SHIP_CRAFT_DEFENSE    ) + rand($this->randStart, $this->randEnd);
      $invasorBomberAttack    += ($invasorBomberInBattle   * Ship::SHIP_BOMBER_ATTACK    ) + rand($this->randStart, $this->randEnd);
      $invasorBomberDefense   += ($invasorBomberInBattle   * Ship::SHIP_BOMBER_DEFENSE   ) + rand($this->randStart, $this->randEnd);
      $invasorCruiserAttack   += ($invasorCruiserInBattle  * Ship::SHIP_CRUISER_ATTACK   ) + rand($this->randStart, $this->randEnd);
      $invasorCruiserDefense  += ($invasorCruiserInBattle  * Ship::SHIP_CRUISER_DEFENSE  ) + rand($this->randStart, $this->randEnd);
      $invasorScoutAttack     += ($invasorScoutInBattle    * Ship::SHIP_SCOUT_ATTACK     ) + rand($this->randStart, $this->randEnd);
      $invasorScoutDefense    += ($invasorScoutInBattle    * Ship::SHIP_SCOUT_DEFENSE    ) + rand($this->randStart, $this->randEnd);
      $invasorStealthAttack   += ($invasorStealthInBattle  * Ship::SHIP_STEALTH_ATTACK   ) + rand($this->randStart, $this->randEnd);
      $invasorStealthDefense  += ($invasorStealthInBattle  * Ship::SHIP_STEALTH_DEFENSE  ) + rand($this->randStart, $this->randEnd);
      $invasorFlagshipAttack  += ($invasorFlagshipInBattle * Ship::SHIP_FLAGSHIP_ATTACK  ) + rand($this->randStart, $this->randEnd);
      $invasorFlagshipDefense += ($invasorFlagshipInBattle * Ship::SHIP_FLAGSHIP_DEFENSE ) + rand($this->randStart, $this->randEnd);
    }

    foreach ($locals as $local) {

      # Adjust ships to battle field size
      $localCraftInBattle = $local->craft > $this->battleFieldSize ? $this->battleFieldSize : $local->craft;
      $localBomberInBattle = $local->bomber > $this->battleFieldSize ? $this->battleFieldSize : $local->bomber;
      $localCruiserInBattle = $local->cruiser > $this->battleFieldSize ? $this->battleFieldSize : $local->cruiser;
      $localScoutInBattle = $local->scout > $this->battleFieldSize ? $this->battleFieldSize : $local->scout;
      $localStealthInBattle = $local->stealth > $this->battleFieldSize ? $this->battleFieldSize : $local->stealth;
      $localFlagshipInBattle = $local->flagship > $this->battleFieldSize ? $this->battleFieldSize : $local->flagship;

      # Calculate attack and defense
      $localCraftAttack     += ($localCraftInBattle    * Ship::SHIP_CRAFT_ATTACK     ) + rand($this->randStart, $this->randEnd);
      $localCraftDefense    += ($localCraftInBattle    * Ship::SHIP_CRAFT_DEFENSE    ) + rand($this->randStart, $this->randEnd);
      $localBomberAttack    += ($localBomberInBattle   * Ship::SHIP_BOMBER_ATTACK    ) + rand($this->randStart, $this->randEnd);
      $localBomberDefense   += ($localBomberInBattle   * Ship::SHIP_BOMBER_DEFENSE   ) + rand($this->randStart, $this->randEnd);
      $localCruiserAttack   += ($localCruiserInBattle  * Ship::SHIP_CRUISER_ATTACK   ) + rand($this->randStart, $this->randEnd);
      $localCruiserDefense  += ($localCruiserInBattle  * Ship::SHIP_CRUISER_DEFENSE  ) + rand($this->randStart, $this->randEnd);
      $localScoutAttack     += ($localScoutInBattle    * Ship::SHIP_SCOUT_ATTACK     ) + rand($this->randStart, $this->randEnd);
      $localScoutDefense    += ($localScoutInBattle    * Ship::SHIP_SCOUT_DEFENSE    ) + rand($this->randStart, $this->randEnd);
      $localStealthAttack   += ($localStealthInBattle  * Ship::SHIP_STEALTH_ATTACK   ) + rand($this->randStart, $this->randEnd);
      $localStealthDefense  += ($localStealthInBattle  * Ship::SHIP_STEALTH_DEFENSE  ) + rand($this->randStart, $this->randEnd);
      $localFlagshipAttack  += ($localFlagshipInBattle * Ship::SHIP_FLAGSHIP_ATTACK  ) + rand($this->randStart, $this->randEnd);
      $localFlagshipDefense += ($localFlagshipInBattle * Ship::SHIP_FLAGSHIP_DEFENSE ) + rand($this->randStart, $this->randEnd);
    }

    # Calculate demage
    $invasorCraftDemage = $invasorCraftAttack - $localCraftDefense;
    $localCraftDemage = $localCraftAttack - $invasorCraftDefense;
    $invasorBomberDemage = $invasorBomberAttack - $localBomberDefense;
    $localBomberDemage = $localBomberAttack - $invasorBomberDefense;
    $invasorCruiserDemage = $invasorCruiserAttack - $localCruiserDefense;
    $localCruiserDemage = $localCruiserAttack - $invasorCruiserDefense;
    $invasorScoutDemage = $invasorScoutAttack - $localScoutDefense;
    $localScoutDemage = $localScoutAttack - $invasorScoutDefense;
    $invasorStealthDemage = $invasorStealthAttack - $localStealthDefense;
    $localStealthDemage = $localStealthAttack - $invasorStealthDefense;
    $invasorFlagshipDemage = $invasorFlagshipAttack - $localFlagshipDefense;
    $localFlagshipDemage = $localFlagshipAttack - $invasorFlagshipDefense;

    # get effect locals
    if ($combat->planet == $local->planet) {
      $effects = $this->getDemageEffects($combat, $invasors[0]->strategy, $locals[0]->strategy, true);
    } else {
      $effects = $this->getDemageEffects($combat, $locals[0]->strategy, $invasors[0]->strategy);
    }

    # Apply demage
    foreach ($locals as $local) {
      if ($invasorCraftDemage > 0) {
        if ($local->craft > 0) {
          $local = $this->applyDemage($invasorCraftDemage, $local, Ship::SHIP_CRAFT_HP, count($locals), $effects, 'craft');
          $invasorCraftDemage = 0;
        }
      }
      $invasorBomberDemage += $invasorCraftDemage;

      if ($invasorBomberDemage > 0) {
        if ($local->bomber > 0) {
          $local = $this->applyDemage($invasorBomberDemage, $local, Ship::SHIP_BOMBER_HP, count($locals), $effects, 'bomber');
          $invasorBomberDemage = 0;
        }
      }
      $invasorCruiserDemage += $invasorBomberDemage;

      if ($invasorCruiserDemage > 0) {
        if ($local->cruiser > 0) {
          $local = $this->applyDemage($invasorCruiserDemage, $local, Ship::SHIP_CRUISER_HP, count($locals), $effects, 'cruiser');
          $invasorCruiserDemage = 0;
        }
      }
      $invasorScoutDemage += $invasorCruiserDemage;

      if ($invasorScoutDemage > 0) {
        if ($local->scout > 0) {
          $local = $this->applyDemage($invasorScoutDemage, $local, Ship::SHIP_SCOUT_HP, count($locals), $effects, 'scout');
          $invasorScoutDemage = 0;
        }
      }
      $invasorStealthDemage += $invasorScoutDemage;

      if ($invasorStealthDemage > 0) {
        if ($local->stealth > 0) {
          $local = $this->applyDemage($invasorStealthDemage, $local, Ship::SHIP_STEALTH_HP, count($locals), $effects, 'stealth');
          $invasorStealthDemage = 0;
        }
      }
      $invasorFlagshipDemage += $invasorStealthDemage;

      if ($localFlagshipDemage > 0) {
        if ($local->flagship > 0) {
          $local = $this->applyDemage($invasorFlagshipDemage, $local, Ship::SHIP_FLAGSHIP_HP, count($locals), $effects, 'flagship');
          $invasorFlagshipDemage = 0;
        }
      }

      // Sincroniza a frota de naves do dono do planeta
      if ($combat->planet == $local->planet) {
        $planet = Planet::find($combat->planet);
        $this->sincronizeFleet($planet, $local);
      }

      $local->save();
    }

    # get effect invasors
    $effects = $this->getDemageEffects($combat, $invasors[0]->strategy, $locals[0]->strategy);

    foreach ($invasors as $invasor) {
      if ($localCraftDemage > 0) {
        if ($invasor->craft > 0) {
          $invasor = $this->applyDemage($localCraftDemage, $invasor, Ship::SHIP_CRAFT_HP, count($invasors), $effects, 'craft');
          $localCraftDemage = 0;
        }
      }
      $localBomberDemage += $localCraftDemage;

      if ($localBomberDemage > 0) {
        if ($invasor->bomber > 0) {
          $invasor = $this->applyDemage($localBomberDemage, $invasor, Ship::SHIP_BOMBER_HP, count($invasors), $effects, 'bomber');
          $localBomberDemage = 0;
        }
      }
      $localCruiserDemage += $localBomberDemage;

      if ($localCruiserDemage > 0) {
        if ($invasor->cruiser > 0) {
          $invasor = $this->applyDemage($localCruiserDemage, $invasor, Ship::SHIP_CRUISER_HP, count($invasors), $effects, 'cruiser');
          $localCruiserDemage = 0;
        }
      }
      $localScoutDemage += $localCruiserDemage;

      if ($localScoutDemage > 0) {
        if ($invasor->scout > 0) {
          $invasor = $this->applyDemage($localScoutDemage, $invasor, Ship::SHIP_SCOUT_HP, count($invasors), $effects, 'scout');
          $localScoutDemage = 0;
        }
      }
      $localStealthDemage += $localScoutDemage;

      if ($localStealthDemage > 0) {
        if ($invasor->stealth > 0) {
          $invasor = $this->applyDemage($localStealthDemage, $invasor, Ship::SHIP_STEALTH_HP, count($invasors), $effects, 'stealth');
          $localStealthDemage = 0;
        }
      }
      $localFlagshipDemage += $localStealthDemage;

      if ($localFlagshipDemage > 0) {
        if ($invasor->flagship > 0) {
          $invasor = $this->applyDemage($localFlagshipDemage, $invasor, Ship::SHIP_FLAGSHIP_HP, count($invasors), $effects, 'flagship');
          $localFlagshipDemage = 0;
        }
      }

      $invasor->save();
    }
  }
}
