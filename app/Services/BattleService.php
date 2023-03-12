<?php

namespace App\Services;

use App\Models\Battle;
use App\Jobs\BattleJob;

class BattleService
{

    public function startNewBattle ($attacker, $defender, $aUnits, $dUnits, $aStrategy, $dStrategy) {
        $battle = new Battle();
        $battle->attacker = $attacker;
        $battle->defender = $defender;
        $battle->attackerUnits = $aUnits;
        $battle->defenderUnits = $dUnits;
        $battle->attackerStrategy = $aStrategy;
        $battle->defenderStrategy = $dStrategy;
        $battle->stage = 0;
        $battle->start = time();
        $battle->attackerRetreated = false;
        $battle->defenderRetreated = true;
        $battle->save();
        $battle = $this->calculateStage($battle);
    }

    public function calculateStage($battle) {
        $this->fillSlots($battle);

        $battle->stage += 1;
        $battle->save();

        # Start job for new stage if no end
        if (! $this->isEnd($battle)) {
            BattleJob::dispatch(
                $this,
                $battle
            )->delay(now()->addSeconds(env("TRITIUM_STAGE_SPEED") / 1000 ));
        } else {
            if ($battle->attackerUnits == 0 || $battle->attackerRetreated = true) {
                $battle->result = 2;
            }
            if ($battle->defenderUnits == 0 || $battle->defenderRetreated = true) {
                $battle->result = 1;
            }
            $battle->save();
        }
    }

    private function fillSlots($battle) {

    }

    private function isEnd($batle) {
        if (
            $battle->attackerUnits == 0 || 
            $battle->defenderUnits == 0 || 
            $battle->attackerRetreated = true || 
            $battle->defenderRetreated = true ) {
            return true;
        } else {
            return false;
        }
    }

    
}