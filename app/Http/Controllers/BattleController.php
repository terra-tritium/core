<?php

namespace App\Http\Controllers;

use App\Models\AttackMode;
use App\Models\DefenseMode;
use App\Models\Player;
use Illuminate\Http\Request;

class BattleController extends Controller
{
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
}
