<?php

namespace App\Services;

use App\Models\Battle;
use App\Models\BattleSlot;
use App\Models\BattleStage;
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
    private $attackSlots;
    private $defenderSlots;
    private $attackReserve;
    private $defenderReserve;
    private $currentStage;

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
        $this->attackSlots = [];
        $this->defenderSlots = [];
        $this->attackReserve = [];
        $this->defenderReserve = [];
        $this->currentStage = new BattleStage();
    }

    public function startNewBattle ($attacker, $defender, $aUnits, $dUnits, $aStrategy, $dStrategy) {
        $battle = new Battle();
        $battle->attacker = $attacker;
        $battle->defender = $defender;
        $battle->attackerUnits = json_encode($aUnits);
        $battle->defenderUnits = json_encode($dUnits);
        $battle->attackerStrategy = $aStrategy;
        $battle->defenderStrategy = $dStrategy;
        $battle->stage = 0;
        $battle->start = time();
        $battle->save();

        $this->attakerReserve = $aUnits;
        $this->defenderReserve = $dUnits;

        $this->loadReverve($battle);

        $battle = $this->calculateStage($battle->id);
    }

    # Job call
    public function calculateStage($battleId) {

        $battle = Battle::find($battleId);

        $this->fillSlots($battle);

        $battle->stage += 1;
        $battle->save();

        $stage = $this->createNewStage($battle);

        # Start job for new stage if no end
        if (!$this->isEnd()) {
            BattleJob::dispatch(
                $this,
                $battle->id
            )->delay(now()->addSeconds(env("TRITIUM_STAGE_SPEED") / 1000 ));
        } else {
            if ($this->attakerReserve == 0 || $this->currentStage->attackerGaveUp == true) {
                $battle->result = 2;
            }
            if ($this->defenderReserve == 0 || $this->currentStage->defenderGaveUp == true) {
                $battle->result = 1;
            }
            $battle->save();
        }
    }

    private function createNewStage($battle) {

        $desistiu = rand(0, 1);

        $stage = new BattleStage();
        $stage->number = $battle->stage;
        $stage->battle = $battle->id;
        $stage->attackerDemage = 0;
        $stage->defenderDemage = 0;
        $stage->attackerStrategy = $battle->attackerStrategy;
        $stage->defenderStrategy = $battle->defenderStrategy;
        $stage->attackerUnits = $battle->attackerUnits;
        $stage->defenderUnits = $battle->attackerUnits;
        $stage->attakerKills = 0;
        $stage->defenderKills = 0;
        $stage->attackerGaveUp = false;
        $stage->defenderGaveUp = ($desistiu == 1) ? true : false;

        $stage->save();
        $this->currentStage = $stage;

        return $stage;
    }

    private function isEnd() {

        $attackSize = count($this->attackReserve);
        $defenderSize = count($this->defenderReserve);

        if (
            $attackSize == 0 || 
            $defenderSize == 0 || 
            $this->currentStage->attackerGaveUp == true || 
            $this->currentStage->defenderGaveUp == true ) {
            return true;
        } else {
            return false;
        }
    }

    private function calculateRangeSize($batle) {
        $attackSize = count($this->attackReserve);
        $defenderSize = count($this->defenderReserve);

        $smallerUnitsSize = 0;

        if ($attackSize > $defenderSize) {
            $smallerUnitsSize = $defenderSize;
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

        $this->attackSlots = $this->createSlots(
            $battle->attackerStrategy,
            $this->droidSlotSize,
            $this->vehicleSlotSize,
            $this->launchersSlotSize,
            $this->specialSlotSize
        );

        $this->defenderSlots = $this->createSlots(
            $battle->defenderStrategy,
            $this->droidSlotSize,
            $this->vehicleSlotSize,
            $this->launchersSlotSize,
            $this->specialSlotSize
        );

        $this->loadSlots('attack');
        $this->loadSlots('defender');
    }

    private function loadSlots($side) {
        $slot = [];

        $slot = ($side == "attack") ? $this->attackSlots : $this->defenderSlots;

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

        $this->{$side.'Slots'} = $slot;
    }

    private function selectUnits($position, $slot, $side) {

        for ($i=0; $i < 10; $i++) {
            if (!empty($slot[$i]['pos'])) {
                
                if ($slot[$i]['pos'] == $position) {
                    switch ($slot[$i]['type']) {
                        case 'D':
                            $slot[$i]['qtd'] += $this->moveUnits('D', $side, ($this->droidSlotSize - $slot[$i]['qtd']));
                            break;
                        case 'V':
                            $slot[$i]['qtd'] += $this->moveUnits('V', $side, ($this->droidSlotSize - $slot[$i]['qtd']));
                            break;
                        case 'L':
                            $slot[$i]['qtd'] += $this->moveUnits('V', $side, ($this->droidSlotSize - $slot[$i]['qtd']));
                            break;
                        case 'E':
                            $slot[$i]['qtd'] += $this->moveUnits('V', $side, ($this->droidSlotSize - $slot[$i]['qtd']));
                            break;
                    }
                }
            }
        }

        return $slot;
    }

    private function moveUnits($type, $side, $available) {

        $qtdMove = 0;
        $reserve = [];

        if ($side == "attack") {
            $reserve = $this->attackReserve;
        } else {
            $reserve = $this->defenderReserve;
        }

        foreach ($reserve as $troop) {
            if ($type == $troop->type) {
                if ($troop->quantity >= $available) {
                    $qtdMove = $available;
                    $troop->quantity -= $available;
                    return $qtdMove;
                } else {
                    $qtdMove += $troop->quantity;
                    $troop->quantity = 0;
                }
            }
        }

        if ($side == "attack") {
            $this->attackReserve = $reserve;
        } else {
            $this->defenderReserve = $reserve;
        }

        return $qtdMove;
    }

    private function loadReverve($battle) {
        $this->attackReserve = json_decode($battle->attackerUnits);
        $this->defenderReserve = json_decode($battle->defenderUnits);
    }

    private function createSlots($strategy, $dSize, $lSize, $vSize, $sSize) {

        $slotPositions = [
            # Cunha
            1 => [
                ['pos' => 'r1c3', 'type' => 'D', 'size' => $dSize, 'qtd' => 0],
                ['pos' => 'r2c2', 'type' => 'D', 'size' => $dSize, 'qtd' => 0],
                ['pos' => 'r2c4', 'type' => 'D', 'size' => $dSize, 'qtd' => 0],
                ['pos' => 'r3c1', 'type' => 'L', 'size' => $lSize, 'qtd' => 0],
                ['pos' => 'r3c3', 'type' => 'v', 'size' => $vSize, 'qtd' => 0],
                ['pos' => 'r3c5', 'type' => 'L', 'size' => $lSize, 'qtd' => 0],
                ['pos' => 'r1e1', 'type' => 'S', 'size' => $sSize, 'qtd' => 0],
            ],
            # Delta
            2 => [
                ['pos' => 'r1c3', 'type' => 'D', 'size' => $dSize, 'qtd' => 0],
                ['pos' => 'r2c2', 'type' => 'D', 'size' => $dSize, 'qtd' => 0],
                ['pos' => 'r2c4', 'type' => 'D', 'size' => $dSize, 'qtd' => 0],
                ['pos' => 'r3c3', 'type' => 'V', 'size' => $vSize, 'qtd' => 0],
                ['pos' => 'r4c1', 'type' => 'L', 'size' => $lSize, 'qtd' => 0],
                ['pos' => 'r4c3', 'type' => 'L', 'size' => $lSize, 'qtd' => 0],
                ['pos' => 'r1e1', 'type' => 'S', 'size' => $sSize, 'qtd' => 0],
            ],
            # Linha
            3 => [
                ['pos' => 'r1c1', 'type' => 'D', 'size' => $dSize, 'qtd' => 0],
                ['pos' => 'r1c2', 'type' => 'D', 'size' => $dSize, 'qtd' => 0],
                ['pos' => 'r1c3', 'type' => 'D', 'size' => $dSize, 'qtd' => 0],
                ['pos' => 'r1c4', 'type' => 'D', 'size' => $dSize, 'qtd' => 0],
                ['pos' => 'r1c5', 'type' => 'D', 'size' => $dSize, 'qtd' => 0],
                ['pos' => 'r2c3', 'type' => 'V', 'size' => $vSize, 'qtd' => 0],
                ['pos' => 'r1e1', 'type' => 'S', 'size' => $sSize, 'qtd' => 0],
            ],
            # Sniper
            4 => [
                ['pos' => 'r1c1', 'type' => 'D', 'size' => $dSize, 'qtd' => 0],
                ['pos' => 'r1c2', 'type' => 'D', 'size' => $dSize, 'qtd' => 0],
                ['pos' => 'r1c3', 'type' => 'D', 'size' => $dSize, 'qtd' => 0],
                ['pos' => 'r1c4', 'type' => 'D', 'size' => $dSize, 'qtd' => 0],
                ['pos' => 'r2c2', 'type' => 'V', 'size' => $vSize, 'qtd' => 0],
                ['pos' => 'r2c3', 'type' => 'L', 'size' => $lSize, 'qtd' => 0],
                ['pos' => 'r1e1', 'type' => 'S', 'size' => $sSize, 'qtd' => 0],
            ],
            # Coluna
            5 => [
                ['pos' => 'r1c3', 'type' => 'D', 'size' => $dSize, 'qtd' => 0],
                ['pos' => 'r2c3', 'type' => 'D', 'size' => $dSize, 'qtd' => 0],
                ['pos' => 'r3c3', 'type' => 'D', 'size' => $dSize, 'qtd' => 0],
                ['pos' => 'r4c3', 'type' => 'L', 'size' => $lSize, 'qtd' => 0],
                ['pos' => 'r5c3', 'type' => 'L', 'size' => $lSize, 'qtd' => 0],
                ['pos' => 'r6c3', 'type' => 'V', 'size' => $vSize, 'qtd' => 0],
                ['pos' => 'r1e1', 'type' => 'S', 'size' => $sSize, 'qtd' => 0],
            ],
            # Diamante
            6 => [
                ['pos' => 'r1c3', 'type' => 'D', 'size' => $dSize, 'qtd' => 0],
                ['pos' => 'r2c1', 'type' => 'L', 'size' => $lSize, 'qtd' => 0],
                ['pos' => 'r2c2', 'type' => 'L', 'size' => $lSize, 'qtd' => 0],
                ['pos' => 'r2c4', 'type' => 'L', 'size' => $lSize, 'qtd' => 0],
                ['pos' => 'r2c5', 'type' => 'L', 'size' => $lSize, 'qtd' => 0],
                ['pos' => 'r3c3', 'type' => 'V', 'size' => $vSize, 'qtd' => 0],
                ['pos' => 'r1e1', 'type' => 'S', 'size' => $sSize, 'qtd' => 0],
            ],
            # Estrela
            7 => [
                ['pos' => 'r1c3', 'type' => 'D', 'size' => $dSize, 'qtd' => 0],
                ['pos' => 'r2c2', 'type' => 'D', 'size' => $dSize, 'qtd' => 0],
                ['pos' => 'r2c4', 'type' => 'D', 'size' => $dSize, 'qtd' => 0],
                ['pos' => 'r3c3', 'type' => 'V', 'size' => $vSize, 'qtd' => 0],
                ['pos' => 'r4c2', 'type' => 'L', 'size' => $lSize, 'qtd' => 0],
                ['pos' => 'r4c4', 'type' => 'L', 'size' => $lSize, 'qtd' => 0],
                ['pos' => 'r1e1', 'type' => 'S', 'size' => $sSize, 'qtd' => 0],
            ],
            # Diagonal
            8 => [
                ['pos' => 'r1c1', 'type' => 'D', 'size' => $dSize, 'qtd' => 0],
                ['pos' => 'r2c2', 'type' => 'D', 'size' => $dSize, 'qtd' => 0],
                ['pos' => 'r3c3', 'type' => 'L', 'size' => $lSize, 'qtd' => 0],
                ['pos' => 'r4c2', 'type' => 'L', 'size' => $lSize, 'qtd' => 0],
                ['pos' => 'r4c4', 'type' => 'L', 'size' => $lSize, 'qtd' => 0],
                ['pos' => 'r5c5', 'type' => 'V', 'size' => $vSize, 'qtd' => 0],
                ['pos' => 'r1e1', 'type' => 'S', 'size' => $sSize, 'qtd' => 0],
            ],
            # Coluna Dupla
            9 => [
                ['pos' => 'r1c2', 'type' => 'D', 'size' => $dSize, 'qtd' => 0],
                ['pos' => 'r1c4', 'type' => 'D', 'size' => $dSize, 'qtd' => 0],
                ['pos' => 'r2c2', 'type' => 'D', 'size' => $dSize, 'qtd' => 0],
                ['pos' => 'r2c4', 'type' => 'V', 'size' => $vSize, 'qtd' => 0],
                ['pos' => 'r3c2', 'type' => 'L', 'size' => $lSize, 'qtd' => 0],
                ['pos' => 'r3c4', 'type' => 'L', 'size' => $lSize, 'qtd' => 0],
                ['pos' => 'r1e1', 'type' => 'S', 'size' => $sSize, 'qtd' => 0],
            ],
            # Flancos
            10 => [
                ['pos' => 'r1c1', 'type' => 'D', 'size' => $dSize, 'qtd' => 0],
                ['pos' => 'r1c5', 'type' => 'D', 'size' => $dSize, 'qtd' => 0],
                ['pos' => 'r2c1', 'type' => 'D', 'size' => $dSize, 'qtd' => 0],
                ['pos' => 'r2c5', 'type' => 'V', 'size' => $vSize, 'qtd' => 0],
                ['pos' => 'r3c1', 'type' => 'L', 'size' => $lSize, 'qtd' => 0],
                ['pos' => 'r3c5', 'type' => 'L', 'size' => $lSize, 'qtd' => 0],
                ['pos' => 'r1e1', 'type' => 'S', 'size' => $sSize, 'qtd' => 0],
            ],
        ];
        
        return $slotPositions[$strategy];
    }

}
