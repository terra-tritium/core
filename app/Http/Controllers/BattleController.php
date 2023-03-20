<?php

namespace App\Http\Controllers;

use App\Models\AttackMode;
use App\Models\DefenseMode;
use App\Models\Player;
use App\Services\BattleService;
use Illuminate\Http\Request;

class BattleController extends Controller
{

    protected $battleService;

    public function __construct(BattleService $battleService) {
        $this->battleService = $battleService;
    }

    public function attackModeList() {
        return AttackMode::orderBy("code")->get();
    }

    public function defenseModeList() {
        return DefenseMode::orderBy("code")->get();
    }

    public function changeAttackMode($address, $option) {
        $player = Player::where("address", $address)->firstOrFail();
        $player->attackMode = $option;
        $player->save();
    }

    public function changeDefenseMode($address, $option) {
        $player = Player::where("address", $address)->firstOrFail();
        $player->defenseMode = $option;
        $player->save();
    }

    public function start() {
        $attacker = "terraSihduam34a43j4hssz94e";
        $defender = "terra9d8sksfdccfkkkllssGu9";
        $aUnits = [
            [
                'unit' => 1,
                'quantity' => 5000,
                'type' => 'D'
            ]
        ];
        $dUnits = [
            [
                'unit' => 1,
                'quantity' => 1000,
                'type' => 'D'
            ]
        ];
        $aStrategy = 3;
        $dStrategy = 5;

        return $this->battleService->startNewBattle (
            $attacker,
            $defender,
            $aUnits,
            $dUnits,
            $aStrategy,
            $dStrategy
        );
    }
}
