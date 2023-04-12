<?php

namespace App\Http\Controllers;

use App\Models\AttackMode;
use App\Models\DefenseMode;
use App\Models\Player;
use App\Models\Battle;
use App\Models\BattleStage;
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

    public function changeAttackMode($option) {
        $user = auth()->user()->id;
        $player = Player::where("user", $user)->firstOrFail();
        $player->attackMode = $option;
        $player->save();
    }

    public function changeDefenseMode($option) {
        $user = auth()->user()->id;
        $player = Player::where("user", $user)->firstOrFail();
        $player->defenseMode = $option;
        $player->save();
    }

    public function view ($id) {
        return Battle::find($id);
    }

    public function stages ($id) {
        return BattleStage::where('battle', $id)->get();
    }

    public function start() {
        $attack = "terraSihduam34a43j4hssz94e";
        $defender = "terra9d8sksfdccfkkkllssGu9";
        $aUnits = [
            [
                'unit' => 1,
                'quantity' => 5000,
                'type' => 'D',
                'attack' => 5,
                'defense' => 2,
                'life' => 20
            ]
        ];
        $dUnits = [
            [
                'unit' => 1,
                'quantity' => 1000,
                'type' => 'D',
                'attack' => 10,
                'defense' => 3,
                'life' => 20
            ]
        ];
        $aStrategy = 3;
        $dStrategy = 5;

        return $this->battleService->startNewBattle (
            $attack,
            $defender,
            $aUnits,
            $dUnits,
            $aStrategy,
            $dStrategy
        );
    }
}
