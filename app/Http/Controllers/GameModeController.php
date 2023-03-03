<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\GameMode;
use App\Models\Player;
use App\Models\Effect;
use Illuminate\Http\Request;

class GameModeController extends Controller
{

    public function list() {
        return GameMode::orderBy('code')->get();
    }

    public function change($address, $code) {
        $p1 = Player::where("address", $address)->firstOrFail();
        $p1->gameMode($code);
        $this->applyEffect($address, $code);
        $p1->save();
    }

    private function applyEffect($address, $code) {
        $effect = GameMode::where("address", $address)->first();
        
        if (!$effect) {
           $effect = new Effect(); 
        }
        
        switch($code) {
            # Titan
            case 2 : 
                $effect->speedProduceUnit = 20;
                $effect->extraAttack = 2;
                $effect->speedResearch = -20;
                $effect->speedMining = -20;
                break;
            # Researcher
            case 3 : 
                $effect->speedResearch = 20;
                $effect->costBuild = 20;
                break;
            # Engineer
            case 3 : 
                $effect->speedProduceShip = 20;
                $effect->speedResearch = -20;
                break;
            # Protector
            case 3 : 
                $effect->protect = 20;
                break;
            # Builder
            case 3 : 
                $effect->costBuild = -20;
                $effect->speedProduceShip = -20;
                $effect->speedProduceUnit = -20;
                break;
            # Navigator
            case 3 : 
                $effect->speedTravel = 20;
                $effect->speedProduceShip = -20;
                $effect->speedProduceUnit = -20;
                break;
            # Miner
            case 3 : 
                $effect->speedMining = 2;
                $effect->protect = -20;
                break;
        }

        $effect->save();
    }
}
