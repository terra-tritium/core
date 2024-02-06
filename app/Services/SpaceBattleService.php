<?php

namespace App\Services;

use App\Models\Battle;
use App\Models\BattleStage;
use App\Models\Fighters;
use App\Jobs\BattleJob;
use App\Models\Building;
use App\Models\Travel;
use App\Models\Troop;

class BattleService
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
        $this->currentStage = new BattleStage();
    }

    public function startNewBattle ($invasor, $local, $invasorUnits, $localUnits, $invasorStrategy, $localStrategy, $planet) {

      $battle = new Battle();
      $battle->planet = $planet;
      $battle->status = Battle::STATUS_CREATE;
      $battle->start = time();
      $battle->stage = 0;
      $battle->invasorUnits = json_encode($invasorUnits);
      $battle->localUnits = json_encode($localUnits);
      $battle->invasorSlots = json_encode("{}");
      $battle->localSlots = json_encode("{}");
      $battle->save();
      
      $player1 = new Fighters();
      $player1->battle = $battle->id;
      $player1->player = $invasor;
      $player1->side = Battle::SIDE_INVASOR;
      $player1->strategy = $invasorStrategy;
      $player1->demage = 0;
      $player1->start = time();
      $player1->stage = 0;
      $player1->planet = $planet;
      $player1->units = json_encode($invasorUnits);
      $player1->save();

      $player2 = new Fighters();
      $player2->battle = $battle->id;
      $player2->player = $local;
      $player2->side = Battle::SIDE_LOCAL;
      $player2->strategy = $localStrategy;
      $player2->demage = 0;
      $player2->start = time();
      $player2->stage = 0;
      $player2->planet = $planet;
      $player2->units = json_encode($localUnits);
      $player2->save();
    }
    
    
}
