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
    private $defenseSlots;
    private $attackReserve;
    private $defenseReserve;
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
        $this->defenseSlots = [];
        $this->attackReserve = [];
        $this->defenseReserve = [];
        $this->currentStage = new BattleStage();
    }

    public function startNewBattle ($attack, $defense, $aUnits, $dUnits, $aStrategy, $dStrategy) {
        $battle = new Battle();
        $battle->attack = $attack;
        $battle->defense = $defense;
        $battle->attackUnits = json_encode($aUnits);
        $battle->defenseUnits = json_encode($dUnits);
        $battle->attackStrategy = $aStrategy;
        $battle->defenseStrategy = $dStrategy;
        $battle->stage = 0;
        $battle->start = time();
        $battle->save();

        $this->attackReserve = $aUnits;
        $this->defenseReserve = $dUnits;

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
            if ($this->attackReserve == 0 || $this->currentStage->attackGaveUp == true) {
                $battle->result = 2;
            }
            if ($this->defenseReserve == 0 || $this->currentStage->defenseGaveUp == true) {
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
        $stage->attackDemage = 0;
        $stage->defenseDemage = 0;
        $stage->attackStrategy = $battle->attackStrategy;
        $stage->defenseStrategy = $battle->defenseStrategy;
        $stage->attackUnits = $battle->attackUnits;
        $stage->defenseUnits = $battle->defenseUnits;
        $stage->attackKills = 0;
        $stage->defenseKills = 0;
        $stage->attackGaveUp = false;
        $stage->defenseGaveUp = ($desistiu == 1) ? true : false;

        $stage = $this->resolveConfrontation($battle, $stage);

        $stage->save();
        $this->currentStage = $stage;

        return $stage;
    }

    private function resolveConfrontation($battle, $stage) {

        $aSlots = $this->attackSlots;
        $dSlots = $this->defenseSlots;
        $stage->attackSlots = json_encode($aSlots);
        $stage->defenseSlots = json_encode($dSlots);

        # execute attack
        for ($i=0; $i < count($aSlots); $i++) {

            if ($aSlots[$i]['qtd'] > 0) {

                for ($j=0; $j < count($dSlots); $j++) {
                    $demage = 0;
                    $kills = 0;
                    $units = [];
                    
                    if ($dSlots[$j]['qtd'] > 0) {

                        $demage = (($aSlots[$i]['attack'] * $aSlots[$i]['size']) - ($dSlots[$j]['defense'] * $dSlots[$j]['size']));
                        if ($demage < 0) { $demage = 0; }

                        $kills = floor($demage / $dSlots[$j]['life']);

                        if ($dSlots[$j]['qtd'] - $kills < 0) {
                            $dSlots[$j]['kills'] += $dSlots[$j]['qtd'];
                            $dSlots[$j]['qtd'] = 0;
                        } else {
                            $dSlots[$j]['kills'] += $kills;
                            $dSlots[$j]['qtd'] -= $kills;
                        }

                        $stage->defenseDemage += $demage;
                        $stage->defenseKills = $dSlots[$j]['kills'];
                        $stage->defenseSlots = json_encode($dSlots);
                        break;
                    }
                }
            }
        }

        #execute defense
        for ($i=0; $i < count($dSlots); $i++) {

            if ($dSlots[$i]['qtd'] > 0) {

                for ($j=0; $j < count($aSlots); $j++) {
                    $demage = 0;
                    $kills = 0;
                    $units = [];
                    
                    if ($aSlots[$j]['qtd'] > 0) {

                        $demage = (($dSlots[$i]['attack'] * $dSlots[$i]['size']) - ($aSlots[$j]['defense'] * $aSlots[$j]['size']));
                        if ($demage < 0) { $demage = 0; }

                        $kills = floor($demage / $aSlots[$j]['life']);

                        if ($aSlots[$j]['qtd'] - $kills < 0) {
                            $aSlots[$j]['kills'] += $aSlots[$j]['qtd'];
                            $aSlots[$j]['qtd'] = 0;
                        } else {
                            $aSlots[$j]['kills'] += $kills;
                            $aSlots[$j]['qtd'] -= $kills;
                        }

                        $stage->attackDemage += $demage;
                        $stage->attackKills = $aSlots[$j]['kills'];
                        $stage->attackSlots = json_encode($aSlots);
                        break;
                    }
                }
            }
        }

        $stage->attackReserve = json_encode($this->attackReserve);
        $stage->defenseReserve = json_encode($this->defenseReserve);

        return $stage;
    }

    private function isEnd() {

        $attackSize = count($this->attackReserve);
        $defenseSize = count($this->defenseReserve);

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

    private function calculateRangeSize($batle) {
        $attackSize = count($this->attackReserve);
        $defenseSize = count($this->defenseReserve);

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

        $this->attackSlots = $this->createSlots(
            $battle->attackStrategy,
            $this->droidSlotSize,
            $this->vehicleSlotSize,
            $this->launchersSlotSize,
            $this->specialSlotSize
        );

        $this->defenseSlots = $this->createSlots(
            $battle->defenseStrategy,
            $this->droidSlotSize,
            $this->vehicleSlotSize,
            $this->launchersSlotSize,
            $this->specialSlotSize
        );

        $this->loadSlots('attack');
        $this->loadSlots('defense');
    }

    private function loadSlots($side) {
        $slot = [];

        $slot = ($side == "attack") ? $this->attackSlots : $this->defenseSlots;

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
                            $slot[$i] = $this->moveUnits($slot[$i], 'D', $side, ($this->droidSlotSize - $slot[$i]['qtd']));
                            break;
                        case 'V':
                            $slot[$i] = $this->moveUnits($slot[$i], 'V', $side, ($this->droidSlotSize - $slot[$i]['qtd']));
                            break;
                        case 'L':
                            $slot[$i] = $this->moveUnits($slot[$i], 'V', $side, ($this->droidSlotSize - $slot[$i]['qtd']));
                            break;
                        case 'E':
                            $slot[$i] = $this->moveUnits($slot[$i], 'V', $side, ($this->droidSlotSize - $slot[$i]['qtd']));
                            break;
                    }
                }
            }
        }

        return $slot;
    }

    private function moveUnits($slot, $type, $side, $available) {

        $qtdMove = 0;
        $attackLevel = 0;
        $defenseLevel = 0;
        $life = 0;
        $reserve = [];

        if ($side == "attack") {
            $reserve = $this->attackReserve;
        } else {
            $reserve = $this->defenseReserve;
        }

        foreach ($reserve as $troop) {
            if ($type == $troop->type) {
                if ($troop->quantity >= $available) {
                    $qtdMove = $available;
                    $troop->quantity -= $available;
                } else {
                    $qtdMove += $troop->quantity;
                    $troop->quantity = 0;
                }
                $attackLevel = $troop->attack;
                $attackDefense = $troop->defense;
                $life = $troop->life;
            }
        }

        if ($side == "attack") {
            $this->attackReserve = $reserve;
        } else {
            $this->defenseReserve = $reserve;
        }

        $slot['qtd'] = $qtdMove;
        $slot['attack'] = $attackLevel;
        $slot['defense'] = $defenseLevel;
        $slot['life'] = $life;

        return $slot;
    }

    private function loadReverve($battle) {
        $this->attackReserve = json_decode($battle->attackUnits);
        $this->defenseReserve = json_decode($battle->defenseUnits);
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

}
