<?php

namespace App\Services;

use App\Models\Combat;
use App\Models\CombatStage;
use App\Models\Fighters;
use App\Jobs\CombatJob;
use App\Models\Building;
use App\Models\Travel;
use App\Models\Troop;

class CombatService
{
    private $sizeFator1;
    private $sizeFator2;
    private $sizeFator3;
    private $droidSlotSize;
    private $vehicleSlotSize;
    private $launchersSlotSize;
    private $specialSlotSize;
    private $rangeLimit1;
    private $rangeLimit2;
    private $rangeLimit3;
    private $currentStage;
    private $attackReserve;
    private $defenseReserve;
    private $travelService;
        

    public function __construct() {
        $this->sizeFator1 = 1;
        $this->sizeFator2 = 5;
        $this->sizeFator3 = 10;
        $this->droidSlotSize = 50;
        $this->vehicleSlotSize = 20;
        $this->launchersSlotSize = 20;
        $this->specialSlotSize = 5;
        $this->rangeLimit1 = 1000;
        $this->rangeLimit2 = 5000;
        $this->rangeLimit3 = -1;
        $this->currentStage = new CombatStage();
    }

    public function startNewCombat ($attack, $defense, $aUnits, $dUnits, $aStrategy, $dStrategy, $dPlanet) {

        $combat = new Combat();
        $combat->planet = $dPlanet;
        $combat->status = 0;
        $combat->start = time();
        $combat->stage = 0;
        $combat->attackUnits   = json_encode($aUnits);
        $combat->defenseUnits  = json_encode($dUnits);
        $combat->attackSlots = json_encode("{}");
        $combat->defenseSlots = json_encode("{}");
        $combat->save();
       
        $player1 = new Fighters();
        $player1->combat = $combat->id;
        $player1->player = $attack;
        $player1->side = 1;
        $player1->strategy = $aStrategy;
        $player1->demage = 0;
        $player1->start = time();
        $player1->stage = 0;
        $player1->planet = $dPlanet;
        $player1->units = json_encode($aUnits);
        $player1->save();

        $player2 = new Fighters();
        $player2->combat = $combat->id;
        $player2->player = $defense;
        $player2->side = 2;
        $player2->strategy = $dStrategy;
        $player2->demage = 0;
        $player2->start = time();
        $player2->stage = 0;
        $player2->planet = $dPlanet;
        $player2->units = json_encode($dUnits);
        $player2->save();

        // $combat = $this->calculateStage($combat->id);
    }
    
    # Job call
    public function calculateStage($combatId) {
        
        $combat = Combat::find($combatId);

        $this->loadReserve($combat);

        $combat = $this->fillSlots($combat);

        $combat->stage += 1;

        $combat->save();

        $stage = $this->createNewStage($combat);
        # Start job for new stage if no end
      
        if (!$this->isEnd()) {
            CombatJob::dispatch(
                $this,
                $combat->id
            )->delay(now()->addSeconds(config("app.tritium_stage_speed")));
        } else {
            $attackSize     = $this->getSizeTroop($this->attackReserve);
            $defenseSize    = $this->getSizeTroop($this->defenseReserve);

            if ($attackSize == 0 || $this->currentStage->attackGaveUp == true) {
                $combat->result = 2;
            }
            if ($defenseSize == 0 || $this->currentStage->defenseGaveUp == true) {
                $combat->result = 1;
            }
            $combat->save();
        }
    }

    private function createNewStage($combat) {

        $stage = new CombatStage();
        $stage->number = $combat->stage;
        $stage->combat = $combat->id;
        $stage->attackDemage = 0;
        $stage->defenseDemage = 0;
        $stage->attackStrategy = $combat->attackStrategy;
        $stage->defenseStrategy = $combat->defenseStrategy;
        $stage->attackUnits = $combat->attackUnits;
        $stage->defenseUnits = $combat->defenseUnits;
        $stage->attackKills = json_encode("{}");
        $stage->defenseKills = json_encode("{}");
        $stage->attackGaveUp = false;
        $stage->defenseGaveUp = false;
        
        $stage = $this->resolveConfrontation($combat, $stage);
       
        $stage->save();
        $this->currentStage = $stage;


        return $stage;
    }

    private function resolveConfrontation($combat, $stage) { 
        
        $aSlots =  $combat->attackSlots;
        $dSlots =  $combat->defenseSlots;

        $stage->attackSlots = json_encode($combat->attackSlots);
        $stage->defenseSlots = json_encode($combat->defenseSlots);
       
        # execute attack
        foreach($aSlots as $aSlot) {
         
            if ($aSlot->qtd > 0) {
                 
                foreach($dSlots as $dSlot) {
                   
                    $demage = 0;
                    $kills = 0;

                    if ($dSlot->qtd > 0) {
                        
                        $demage = (($aSlot->attack * $aSlot->qtd) - ($dSlot->defense * $dSlot->qtd));
                        
                        if ($demage >  0) {  

                            $kills = floor($demage / $dSlot->life);
                            
                            if (($dSlot->qtd - $kills) <= 0) {
                                $dSlot->kills += $dSlot->qtd;
                                $dSlot->qtd = 0;
                            } else {
                                $dSlot->kills += $kills;
                                $dSlot->qtd -= $kills;
                            }
                            $stage->defenseDemage += $demage;
                        }
                       
                        $stage->defenseKills = json_encode($dSlot);
                        $stage->defenseSlots = json_encode($dSlots);
                        break;
                    }
                }
            }
        }
      
        #execute defense
        foreach ($dSlots as $dSlot) {

            if ($dSlot->qtd > 0) {
               
                foreach ($aSlots as $aSlot) {
                    
                    $demage = 0;
                    $kills = 0;
                    
                    if ($aSlot->qtd > 0) {
                       
                        $demage = (($dSlot->attack * $dSlot->qtd) - ($aSlot->defense * $aSlot->qtd));
                       
                        if ($demage >  0) {  
                            
                            $kills = floor($demage / $aSlot->life);

                            if (($aSlot->qtd - $kills) <=0)  {
                                $aSlot->kills += $aSlot->qtd;
                                $aSlot->qtd = 0;
                            } else {
                                $aSlot->kills += $kills;
                                $aSlot->qtd -= $kills;
                            }

                            $stage->attackDemage += $demage;
                        }

                        $stage->attackKills = json_encode($aSlot);
                        $stage->attackSlots = json_encode($aSlots);
                        break;
                    }
                }
            }
        }
       
        $stage->attackReserve = json_encode($this->attackReserve);
        $stage->defenseReserve = json_encode($this->defenseReserve);

        $combat->attackReserve = json_encode($this->attackReserve);
        $combat->defenseReserve = json_encode($this->defenseReserve);

        $combat->save();
         
        return $stage;
    }

    private function isEnd() {

        $attackSize     = $this->getSizeTroop($this->attackReserve);
        $defenseSize    = $this->getSizeTroop($this->defenseReserve);
        
        if (
            $attackSize == 0 || 
            $defenseSize == 0 || 
            $this->currentStage->attackGaveUp == true || 
            $this->currentStage->defenseGaveUp == true ) {
            return true;
        } else {
            return false;
        }
    }

    private function calculateRangeSize($combat) {
        
        $attackSize     = $this->getSizeTroop(json_decode($combat->attackUnits));
        $defenseSize    = $this->getSizeTroop(json_decode($combat->defenseUnits));
       
        $smallerUnitsSize = 0;

        if ($attackSize > $defenseSize) {
            $smallerUnitsSize = $defenseSize;
        } else {
            $smallerUnitsSize = $attackSize;
        }

        if ($smallerUnitsSize < $this->rangeLimit1) {
            return 1;
        }

        if ($smallerUnitsSize < $this->rangeLimit2) {
            return 2;
        }

        return 3;
    }

    private function fillSlots($combat) {
      
        $range = $this->calculateRangeSize($combat);
        switch ($range) {
            case 1: 
                $this->droidSlotSize = $this->droidSlotSize * $this->sizeFator1;
                $this->vehicleSlotSize = $this->vehicleSlotSize * $this->sizeFator1;
                $this->launchersSlotSize = $this->launchersSlotSize * $this->sizeFator1;
                $this->specialSlotSize = $this->specialSlotSize * $this->sizeFator1;
                break;
            case 2: 
                $this->droidSlotSize = $this->droidSlotSize * $this->sizeFator2;
                $this->vehicleSlotSize = $this->vehicleSlotSize * $this->sizeFator2;
                $this->launchersSlotSize = $this->launchersSlotSize * $this->sizeFator2;
                $this->specialSlotSize = $this->specialSlotSize * $this->sizeFator2;
                break;
            case 3: 
                $this->droidSlotSize = $this->droidSlotSize * $this->sizeFator2;
                $this->vehicleSlotSize = $this->vehicleSlotSize * $this->sizeFator2;
                $this->launchersSlotSize = $this->launchersSlotSize * $this->sizeFator2;
                $this->specialSlotSize = $this->specialSlotSize * $this->sizeFator2;
                break;
        }
        
        $combat->attackSlots = json_encode($this->createSlots(
            $combat->attackStrategy,
            $this->droidSlotSize,
            $this->vehicleSlotSize,
            $this->launchersSlotSize,
            $this->specialSlotSize
        ));
       
        $combat->defenseSlots = json_encode($this->createSlots(
            $combat->defenseStrategy,
            $this->droidSlotSize,
            $this->vehicleSlotSize,
            $this->launchersSlotSize,
            $this->specialSlotSize
        ));
     
        $combat = $this->loadSlots($combat, 'attack');
        $combat = $this->loadSlots($combat, 'defense');

        return $combat;
    }

    private function loadSlots($combat, $side) {
        $slot = [];
       
        $slot = ($side == "attack") ? $combat->attackSlots : $combat->defenseSlots;
        
        $slot = json_decode($slot); 
        $slot = $this->selectUnits('r1c1', $slot, $side);
        $slot = $this->selectUnits('r1c2', $slot, $side);
        $slot = $this->selectUnits('r1c3', $slot, $side);
        $slot = $this->selectUnits('r1c4', $slot, $side);
        $slot = $this->selectUnits('r1c5', $slot, $side);
        $slot = $this->selectUnits('r2c1', $slot, $side);
        $slot = $this->selectUnits('r2c2', $slot, $side);
        $slot = $this->selectUnits('r2c3', $slot, $side);
        $slot = $this->selectUnits('r2c4', $slot, $side);
        $slot = $this->selectUnits('r2c5', $slot, $side);
        $slot = $this->selectUnits('r3c1', $slot, $side);
        $slot = $this->selectUnits('r3c2', $slot, $side);
        $slot = $this->selectUnits('r3c3', $slot, $side);
        $slot = $this->selectUnits('r3c4', $slot, $side);
        $slot = $this->selectUnits('r3c5', $slot, $side);
        $slot = $this->selectUnits('r4c1', $slot, $side);
        $slot = $this->selectUnits('r4c2', $slot, $side);
        $slot = $this->selectUnits('r4c3', $slot, $side);
        $slot = $this->selectUnits('r4c4', $slot, $side);
        $slot = $this->selectUnits('r4c5', $slot, $side);
        $slot = $this->selectUnits('r5c1', $slot, $side);
        $slot = $this->selectUnits('r5c2', $slot, $side);
        $slot = $this->selectUnits('r5c3', $slot, $side);
        $slot = $this->selectUnits('r5c4', $slot, $side);
        $slot = $this->selectUnits('r5c5', $slot, $side);
        $slot = $this->selectUnits('r1e1', $slot, $side);

        $combat->{$side.'Slots'} = $slot;
        
        return $combat;
    }

    private function selectUnits($position, $slot, $side) {
       
        for ($i=0; $i < 10; $i++) {
            if (!empty($slot[$i]->pos)) {
                
                if ($slot[$i]->pos == $position) {
                    switch ($slot[$i]->type) {
                        case 'D':
                            $slot[$i] = $this->moveUnits($slot[$i], 'D', $side, ($this->droidSlotSize - $slot[$i]->qtd));
                            break;
                        case 'V':
                            $slot[$i] = $this->moveUnits($slot[$i], 'V', $side, ($this->vehicleSlotSize - $slot[$i]->qtd));
                            break;
                        case 'L':
                            $slot[$i] = $this->moveUnits($slot[$i], 'L', $side, ($this->launchersSlotSize - $slot[$i]->qtd));
                            break;
                        case 'E':
                            $slot[$i] = $this->moveUnits($slot[$i], 'E', $side, ($this->specialSlotSize - $slot[$i]->qtd));
                            break;
                    }
                }
            }
        }
       
        return $slot;
    }

    private function moveUnits($slot, $type, $side, $available) {
        
        $quantityTroop = 0;
        $attackLevel = 0;
        $defenseLevel = 0;
        $life = 0;
        $unit = null;
        $reserve = [];
        $different = 0 ; 
    
        if ($side == "attack") {
            $reserve = $this->attackReserve;
        } else {
            $reserve = $this->defenseReserve;
        }
       
        for($i=0; $i < count($reserve) ; $i++){
            if ($type == $reserve[$i]->type &&  $reserve[$i]->quantity > 0 ) {

                $quantityTroop  = $reserve[$i]->quantity;
                $attackLevel    = $reserve[$i]->attack;
                $defenseLevel   = $reserve[$i]->defense;
                $life           = $reserve[$i]->life;
                $unit           = $reserve[$i]->unit;

                $different = $quantityTroop - $available;
                $different = $different > 0 ? $different : 0 ;
                $reserve[$i]->quantity = $different ; 
               
                break;
            }
        }

        $quantityTroop = $quantityTroop - $different;
     
        if ($side == "attack") {
            $this->attackReserve = $reserve;
        } else {
            $this->defenseReserve = $reserve;
        }

        $slot->qtd      = $quantityTroop;
        $slot->attack   = $attackLevel * $quantityTroop;
        $slot->defense  = $defenseLevel * $quantityTroop;
        $slot->life     = $life * $quantityTroop;
        $slot->unit     = $unit;
        
        return $slot;
    }

    private function loadReserve($combat) {
        if($combat->stage == 0){
            $this->attackReserve = json_decode($combat->attackUnits);
            $this->defenseReserve = json_decode($combat->defenseUnits);
        }else{
            $this->attackReserve = json_decode($combat->attackReserve);
            $this->defenseReserve = json_decode($combat->defenseReserve);
        }
    }

    private function createSlots($strategy, $dSize, $lSize, $vSize, $sSize) {

        $slotPositions = [
            # Cunha
            1 => [
                ['pos' => 'r1c3', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'D', 'size' => $dSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r2c2', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'D', 'size' => $dSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r2c4', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'D', 'size' => $dSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r3c1', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'L', 'size' => $lSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r3c3', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'v', 'size' => $vSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r3c5', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'L', 'size' => $lSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r1e1', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'S', 'size' => $sSize, 'qtd' => 0, 'kills' => 0],
            ],
            # Delta
            2 => [
                ['pos' => 'r1c3', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'D', 'size' => $dSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r2c2', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'D', 'size' => $dSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r2c4', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'D', 'size' => $dSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r3c3', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'V', 'size' => $vSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r4c1', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'L', 'size' => $lSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r4c3', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'L', 'size' => $lSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r1e1', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'S', 'size' => $sSize, 'qtd' => 0, 'kills' => 0],
            ],
            # Linha
            3 => [
                ['pos' => 'r1c1', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'D', 'size' => $dSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r1c2', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'D', 'size' => $dSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r1c3', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'D', 'size' => $dSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r1c4', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'D', 'size' => $dSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r1c5', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'D', 'size' => $dSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r2c3', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'V', 'size' => $vSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r1e1', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'S', 'size' => $sSize, 'qtd' => 0, 'kills' => 0],
            ],
            # Sniper
            4 => [
                ['pos' => 'r1c1', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'D', 'size' => $dSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r1c2', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'D', 'size' => $dSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r1c3', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'D', 'size' => $dSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r1c4', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'D', 'size' => $dSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r2c2', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'V', 'size' => $vSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r2c3', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'L', 'size' => $lSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r1e1', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'S', 'size' => $sSize, 'qtd' => 0, 'kills' => 0],
            ],
            # Coluna
            5 => [
                ['pos' => 'r1c3', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'D', 'size' => $dSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r2c3', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'D', 'size' => $dSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r3c3', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'D', 'size' => $dSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r4c3', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'L', 'size' => $lSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r5c3', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'L', 'size' => $lSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r6c3', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'V', 'size' => $vSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r1e1', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'S', 'size' => $sSize, 'qtd' => 0, 'kills' => 0],
            ],
            # Diamante
            6 => [
                ['pos' => 'r1c3', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'D', 'size' => $dSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r2c1', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'L', 'size' => $lSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r2c2', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'L', 'size' => $lSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r2c4', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'L', 'size' => $lSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r2c5', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'L', 'size' => $lSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r3c3', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'V', 'size' => $vSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r1e1', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'S', 'size' => $sSize, 'qtd' => 0, 'kills' => 0],
            ],
            # Estrela
            7 => [
                ['pos' => 'r1c3', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'D', 'size' => $dSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r2c2', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'D', 'size' => $dSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r2c4', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'D', 'size' => $dSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r3c3', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'V', 'size' => $vSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r4c2', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'L', 'size' => $lSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r4c4', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'L', 'size' => $lSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r1e1', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'S', 'size' => $sSize, 'qtd' => 0, 'kills' => 0],
            ],
            # Diagonal
            8 => [
                ['pos' => 'r1c1', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'D', 'size' => $dSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r2c2', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'D', 'size' => $dSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r3c3', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'L', 'size' => $lSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r4c2', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'L', 'size' => $lSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r4c4', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'L', 'size' => $lSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r5c5', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'V', 'size' => $vSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r1e1', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'S', 'size' => $sSize, 'qtd' => 0, 'kills' => 0],
            ],
            # Coluna Dupla
            9 => [
                ['pos' => 'r1c2', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'D', 'size' => $dSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r1c4', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'D', 'size' => $dSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r2c2', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'D', 'size' => $dSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r2c4', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'V', 'size' => $vSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r3c2', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'L', 'size' => $lSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r3c4', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'L', 'size' => $lSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r1e1', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'S', 'size' => $sSize, 'qtd' => 0, 'kills' => 0],
            ],
            # Flancos
            10 => [
                ['pos' => 'r1c1', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'D', 'size' => $dSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r1c5', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'D', 'size' => $dSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r2c1', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'D', 'size' => $dSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r2c5', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'V', 'size' => $vSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r3c1', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'L', 'size' => $lSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r3c5', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'L', 'size' => $lSize, 'qtd' => 0, 'kills' => 0],
                ['pos' => 'r1e1', 'unit' => 1, 'attack' => 0, 'defense' => 0, 'life' => 0, 'type' => 'S', 'size' => $sSize, 'qtd' => 0, 'kills' => 0],
            ],
        ];
        
        return $slotPositions[$strategy];
    }

    private function getSizeTroop($troop){
        $quantityTroop  = 0; 

        for($i=0; $i < count($troop) ; $i++){
            $quantityTroop +=  $troop[$i]->quantity;
        }

        return  $quantityTroop;
    }

    public function travelsData($from)
    {
        $travel = new Travel();
        return $travel->getTravelsData($from);
    }
    public function travelsFinished($travels){
        $finished = [];
        foreach($travels as $travel){
            if($travel->chegou == 1){
                array_push($finished, $travel);
            }
        }
        return $finished;
    }
    /**
     * @todo verificar no banco se possui nave
     */
    public function targetHasShip($target){
        return false;        
    }

    public function targetHasShield($target){
        $build = Building::where(['planet'=>$target, 'build'=>12])->first();
        return $build;
    }
    public function targetHasTroop($target){
        $troops = Troop::where('planet',$target)->get();
        return $troops;
    }
    public function capturarRecurso($travelId,$target, $from){
        $now = time();

        $travel = Travel::findOrFail($travelId);
        $travel->metal = 100;
        $travel->crystal = 100;
        $travel->uranium = 100;
        $travel->action = 2;
        $travel->status = 2;
        $travel->start = $now;
        $travel->arrival = $now + 600 ;
        $travel->save();
        return "capturar recurso do " . $target . " e voltar para " . $from;
    }
}
