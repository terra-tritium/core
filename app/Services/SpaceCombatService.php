<?php

namespace App\Services;

use App\Models\Combat;
use App\Models\Fighters;
use App\Models\Planet;
use App\Models\Ship;
use App\Models\Fleet;
use App\Models\Strategy;
use App\Jobs\SpaceCombatJob;

class SpaceCombatService
{
  private $battleFieldSize;
  private $randStart = 0;
  private $randEnd = 5;

  public function __construct() {
    $this->battleFieldSize = 50;
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
    $player1->transportShip = $travel->transportShip;
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
    $player2->transportShip = $travel->transportShip;
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

    $this->startCombat($combat->id);
  }

  public function startCombat($combatId) {
    $combat = Combat::find($combatId);
    
    if ($combat->status != Combat::STATUS_CREATE) {
      return false;
    }

    $combat->status = Combat::STATUS_RUNNING;
    $combat->stage = 1;
    $combat->nextStage = time() + env('TRITIUM_COMBAT_STAGE_TIME');
    $combat->save();

    # Queue next stage
    SpaceCombatJob::dispatch($combatId)->delay(now()->addSeconds(env('TRITIUM_COMBAT_STAGE_TIME')));
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
      $this->resolve($invasors, $locals);
    } else {
      # Invasors win
      $this->finishCombat($combatId, Combat::SIDE_INVASOR);
      return true;
    }

    if ($this->haveShips($invasors)) {
      $this->resolve($locals, $invasors);
    } else {
      # Locals win
      $this->finishCombat($combatId, Combat::SIDE_LOCAL);
      return true;
    }

    $combat->stage++;
    $combat->nextStage = time() + env('TRITIUM_COMBAT_STAGE_TIME');
    $combat->save();

    # Queue next stage
    SpaceCombatJob::dispatch($combatId)->delay(now()->addSeconds(env('TRITIUM_COMBAT_STAGE_TIME')));
  }

  private function finishCombat($combatId, $winner) {
    $combat = Combat::find($combatId);
    $combat->status = Combat::STATUS_FINISH;
    $combat->winner = $winner;
    $combat->save();
  }

  private function haveShips($fighters) {
    $ships = 0;
    foreach ($fighters as $figther) {
      $ships += $figther->cruiser + $figther->craft + $figther->bomber + $figther->scout + $figther->stealth + $figther->flagship;
    }
    return $ships > 0;
  }

  private function getDemageEffects($p1StrategyId, $p2StrategyId) {
    $p1Strategy = Strategy::find($p1StrategyId);
    $p2Strategy = Strategy::find($p2StrategyId);

    if ($p1Strategy && $p2Strategy) {
      $effects = $p1Strategy->attack - $p2Strategy->defense;
    }

    return $effects;
  }

  private function applyDemage($demage, $figther, $hp, $qtdPlayers, $effects, $ship) {
    $demage = $demage + $effects;
    $kills = $demage / $hp;
    $kills = ceil($kills / $qtdPlayers);
    if ($figther->$ship > 0) {
      $figther->$ship -= $kills;
      if ($figther->$ship < 0) {
        $figther->$ship = 0;
      }
    }
    return $figther;
  }

  private function resolve($invasors, $locals) {

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
    $effects = $this->getDemageEffects($locals[0]->strategy, $invasors[0]->strategy);

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

      $local->save();
    }

    # get effect invasors
    $effects = $this->getDemageEffects($invasors[0]->strategy, $locals[0]->strategy);

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
