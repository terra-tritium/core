<?php

namespace App\Services;

use App\Models\Combat;
use App\Models\CombatStage;
use App\Models\Fighters;
use App\Models\Planet;
use App\Models\Ship;
use App\Jobs\CombatJob;
use App\Models\Building;
use App\Models\Fleet;
use App\Models\Travel;
use App\Models\Troop;

class SpaceCombatService
{
  private $frontLineSize;
  private $longRangeSize;
  private $specialSize;
  private $commandSize;
  
  private $currentStage;
  private $attackReserve;
  private $defenseReserve;
  private $travelService;

  public function __construct() {
    $this->frontLineSize = 50;
    $this->longRangeSize = 20;
    $this->specialSize = 20;
    $this->commandSize = 8;
    $this->currentStage = new CombatStage();
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
    $player2->cruiser = $planet->cruiser;

    $fleet = Fleet::where('planet', $travel->to)->first();
    foreach ($fleet->ships as $ship) {
      switch ($ship->unit) {
        case Ship::SHIP_CRAFT:
          $player2->craft = $ship->quantity;
          break;
        case Ship::SHIP_BOMBER:
          $player2->bomber = $ship->quantity;
          break;
        case Ship::SHIP_CRUISER:
          $player2->cruiser = $ship->quantity;
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

    $player2->save();
  }

  public function startCombat($combatId) {
    $combat = Combat::find($combatId);
    
    if ($combat->status != Combat::STATUS_CREATE) {
      return false;
    }

    $combat->status = Combat::STATUS_RUNNING;
    $combat->stage = 1;
    $combat->save();
  }

  public function excuteStage($combatId) {
    $combat = Combat::find($combatId);

    if ($combat->status == Combat::STATUS_CREATE) {
      $this->startCombat($combatId);
    }

    $invasors = Fighters::where(['combat'=>$combatId, 'side'=>Combat::SIDE_INVASOR])->first();
    $locals = Fighters::where(['combat'=>$combatId, 'side'=>Combat::SIDE_LOCAL])->first();
  }

  private function resolve($invasors, $locals) {
    foreach ($invasors as $invasor) {
      $invasor->demage = 0;
      $invasor->save();
    }
  }
}
