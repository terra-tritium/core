<?php

namespace App\Http\Controllers;

//use App\Models\AttackMode;
//use App\Models\DefenseMode;
use App\Models\Player;
use App\Models\Battle;
use App\Models\BattleStage;
use App\Services\BattleService;
use App\Services\PlayerService;
use Illuminate\Http\Request;

class BattleController extends Controller
{
    protected $battleService;
    protected $playerService;

    public function __construct(BattleService $battleService, PlayerService $playerService) {
        $this->battleService = $battleService;
        $this->playerService = $playerService;
    }

    // public function attackModeList() {
    //     return AttackMode::orderBy("code")->get();
    // }

    // public function defenseModeList() {
    //     return DefenseMode::orderBy("code")->get();
    // }

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

    public function start($defense,$planet) {
        $playerOwnerPlatet = $this->playerService->iSplayerOwnerPlanet($defense,$planet);

        if($playerOwnerPlatet){
            $attack  = Player::getPlayerLogged();
            $defense = Player::find($defense);

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
            
            $aStrategy = $attack->attackStrategy;
            $dStrategy = $defense->defenseStrategy;

            return $this->battleService->startNewBattle (
                $attack->id,
                $defense->id,
                $aUnits,
                $dUnits,
                $aStrategy,
                $dStrategy
            );
        }
        else{
            return response()->json([
                'message' => 'The player is not the owner of the planet'
            ], 403);
        }
    }
}
