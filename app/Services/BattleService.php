<?php

namespace App\Services;

use App\Models\Battle;
use App\Models\BattleStage;
use App\Models\Fighters;
use App\Jobs\BattleJob;

class BattleService
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
        $this->currentStage = new BattleStage();
    }

    public function startNewBattle ($attack, $defense, $aUnits, $dUnits, $aStrategy, $dStrategy, $dPlanet) {

        $battle = new Battle();
        $battle->planet = $dPlanet;
        $battle->status = 0;
        $battle->start = time();
        $battle->stage = 0;
        $battle->save();

        $player1 = new Fighters();
        $player1->battle = $battle->id;
        $player1->player = $attack;
        $player1->side = 1;
        $player1->strategy = $aStrategy;
        $player1->demage = 0;
        $player1->start = time();
        $player1->stage = 0;
        $player1->units = json_encode($aUnits);
        $player1->save();

        $player2 = new Fighters();
        $player2->battle = $battle->id;
        $player2->player = $defense;
        $player2->side = 2;
        $player2->strategy = $dStrategy;
        $player2->demage = 0;
        $player2->start = time();
        $player2->stage = 0;
        $player2->units = json_encode($dUnits);
        $player2->save();

        // //$this->loadReverve($battle);

        // $battle = $this->calculateStage($battle->id);
    }
    
    # Job call
    public function calculateStage($battleId) {
        
        $battle = Battle::find($battleId);

        $this->loadReserve($battle);
        
        $battle = $this->fillSlots($battle);

        $battle->stage += 1;
        $battle->save();
         dd( $this->attackReserve);
        $stage = $this->createNewStage($battle);
       
        # Start job for new stage if no end
        if (!$this->isEnd()) {
            BattleJob::dispatch(
                $this,
                $battle->id
            )->delay(now()->addSeconds(env("TRITIUM_STAGE_SPEED")));
        } else {
            $attackSize     = $this->getSizeTroop($this->attackReserve);
            $defenseSize    = $this->getSizeTroop($this->defenseReserve);

            if ($attackSize == 0 || $this->currentStage->attackGaveUp == true) {
                $battle->result = 2;
            }
            if ($defenseSize == 0 || $this->currentStage->defenseGaveUp == true) {
                $battle->result = 1;
            }
            $battle->save();
        }
    }

    private function createNewStage($battle) {

        $stage = new BattleStage();
        $stage->number = $battle->stage;
        $stage->battle = $battle->id;
        $stage->attackDemage = 0;
        $stage->defenseDemage = 0;
        $stage->attackStrategy = $battle->attackStrategy;
        $stage->defenseStrategy = $battle->defenseStrategy;
        $stage->attackUnits = $battle->attackUnits;
        $stage->defenseUnits = $battle->defenseUnits;
        $stage->attackKills = json_encode("{}");
        $stage->defenseKills = json_encode("{}");
        $stage->attackGaveUp = false;
        $stage->defenseGaveUp = false;

        $stage = $this->resolveConfrontation($battle, $stage);
        
        $stage->save();
        $this->currentStage = $stage;


        return $stage;
    }

    private function resolveConfrontation($battle, $stage) {
        $aSlots =  $battle->attackSlots;
        $dSlots =  $battle->defenseSlots;
        $stage->attackSlots = json_encode($battle->attackSlots);
        $stage->defenseSlots = json_encode($battle->defenseSlots);
       
        # execute attack
        foreach($aSlots as $aSlot) {
         
            if ($aSlot->qtd > 0) {
                 
                foreach($dSlots as $dSlot) {
                    $demage = 0;
                    $kills = 0;
                    $units = [];
                    
                    if ($dSlot->qtd > 0) {
                        /*RVB
                        Sempre será 0, porque na tabela units o valor de attack, defense e size são iguais
                        assim nunca terá mortes a ser descontado  no $demage.
                        Teremos que fazer um calculo quando o valor de kills for igual a zero.
                        */
                        $demage = (($aSlot->attack * $aSlot->size) - ($dSlot->defense * $dSlot->size));
                        
                        if ($demage < 0) { $demage = 0; }

                        $kills = floor($demage / $dSlot->life);
                         
                        if (($dSlot->qtd - $kills) < 0 || $kills == 0) {
                            $dSlot->kills += $dSlot->qtd;
                            $dSlot->qtd = 0;
                        } else {
                            $dSlot->kills += $kills;
                            $dSlot->qtd -= $kills;
                        }

                        $stage->defenseDemage += $demage;
                        //$stage->defenseKills = $dSlot->kills;
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
                        
                        $demage = (($dSlot->attack * $dSlot->size) - ($aSlot->defense * $aSlot->size));
                        if ($demage < 0) { $demage = 0; }
                        /*RVB
                        Sempre será 0, porque na tabela units o valor de attack, defense e size são iguais
                        assim nunca terá mortes a ser descontado  no $demage.
                        Teremos que fazer um calculo quando o valor de kills for igual a zero.
                        */
                        $kills = floor($demage / $aSlot->life);
                        
                        if (($aSlot->qtd - $kills) < 0 || $kills == 0 )  {
                            $aSlot->kills += $aSlot->qtd;
                            $aSlot->qtd = 0;
                        } else {
                            $aSlot->kills += $kills;
                            $aSlot->qtd -= $kills;
                        }

                        $stage->attackDemage += $demage;
                        //$stage->attackKills = $aSlot->kills;
                        $stage->attackKills = json_encode($aSlot);
                        $stage->attackSlots = json_encode($aSlots);
                        break;
                    }
                }
            }
        }
        
        $stage->attackReserve = json_encode($this->attackReserve);
        $stage->defenseReserve = json_encode($this->defenseReserve);

        /*
            Analisar porque isso foi feito, pois o attackReserve e defenseReserve são diferente dos attackSlots e defenseSlots
        */
        //$battle->attackSlots = json_encode($this->attackReserve);
        //$battle->defenseSlots = json_encode($this->defenseReserve);
        
        $battle->attackReserve = json_encode($this->attackReserve);
        $battle->defenseReserve = json_encode($this->defenseReserve);
        $battle->save();
         
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

    private function calculateRangeSize($battle) {
        
        $attackSize     = $this->getSizeTroop(json_decode($battle->attackReserve));
        $defenseSize    = $this->getSizeTroop(json_decode($battle->defenseReserve));
       
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

    private function fillSlots($battle) {

        $range = $this->calculateRangeSize($battle);
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

        $battle->attackSlots = json_encode($this->createSlots(
            $battle->attackStrategy,
            $this->droidSlotSize,
            $this->vehicleSlotSize,
            $this->launchersSlotSize,
            $this->specialSlotSize
        ));

        $battle->defenseSlots = json_encode($this->createSlots(
            $battle->defenseStrategy,
            $this->droidSlotSize,
            $this->vehicleSlotSize,
            $this->launchersSlotSize,
            $this->specialSlotSize
        ));
      
        $battle = $this->loadSlots($battle, 'attack');
        $battle = $this->loadSlots($battle, 'defense');

        return $battle;
    }

    private function loadSlots($battle, $side) {
        $slot = [];
       
        $slot = ($side == "attack") ? $battle->attackSlots : $battle->defenseSlots;
        
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

        $battle->{$side.'Slots'} = $slot;
        
        return $battle;
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
        
        $qtdMove = 0;
        $quantityTroop = 0;
        $attackLevel = 0;
        $defenseLevel = 0;
        $life = 0;
        $reserve = [];
        $j = [];

        if ($side == "attack") {
            $reserve = $this->attackReserve;
        } else {
            $reserve = $this->defenseReserve;
        }
       
        
        for($i=0; $i < count($reserve) ; $i++){
            if ($type == $reserve[$i]->type) {
                $quantityTroop +=  $reserve[$i]->quantity;
                $attackLevel = $reserve[$i]->attack;
                $defenseLevel = $reserve[$i]->defense;
                $life = $reserve[$i]->life;
                $j[] = $i;
            }
        }

        $different = $available - $quantityTroop;
        $q =  0;
        $availableR  =  $available ;

        //Esta errado tem que fazer o calculo apenas em uma unidade até zerar
        foreach($j as $jj){
            $q = $reserve[$jj]->quantity  ;
            if ($different > 0) { 
                //Tropa faltando
                $qtdMove = $quantityTroop;
                $reserve[$jj]->quantity = 0;
            } else { 
                //Tropa sobrando
                $qtdMove =  $available;
                $reserve[$jj]->quantity -= $availableR;
                $reserve[$jj]->quantity = $reserve[$jj]->quantity < 0 ? 0 : $reserve[$jj]->quantity ;
                $availableR   -= $q  ;
               
            }
        }

        if ($side == "attack") {
            $this->attackReserve = $reserve;
        } else {
            $this->defenseReserve = $reserve;
        }

        $slot->qtd = $qtdMove;
        $slot->attack  = $attackLevel;
        $slot->defense  = $defenseLevel;
        $slot->life  = $life;
        
        return $slot;
    }

    private function loadReserve($battle) {
        //$this->attackReserve = json_decode($battle->attackUnits);
        //$this->defenseReserve = json_decode($battle->defenseUnits);

        $this->attackReserve = json_decode($battle->attackReserve);
        $this->defenseReserve = json_decode($battle->defenseReserve);
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
}
