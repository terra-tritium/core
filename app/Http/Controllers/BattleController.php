<?php

namespace App\Http\Controllers;

//use App\Models\AttackMode;
//use App\Models\DefenseMode;
use App\Models\Player;
use App\Models\Battle;
use App\Models\BattleStage;
use App\Services\BattleService;
use App\Services\PlayerService;
use App\Services\TravelService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BattleController extends Controller
{
    protected $battleService;
    protected $playerService;
    protected $travelService;

    public function __construct(BattleService $battleService, PlayerService $playerService, TravelService $travelService) {
        $this->battleService = $battleService;
        $this->playerService = $playerService;
        $this->travelService = $travelService;
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

    public function list () {
        $player = Player::getPlayerLogged();

        if (!$player) {
            return response()->json(['error' => 'Unauthenticated player.'], Response::HTTP_UNAUTHORIZED);
        }

        $battles = Battle::where('player', $player->id)->orderBy('start', 'desc')->limit(12)->get();

        return response()->json($battles);
    }

    public function stages ($id) {
        return BattleStage::where('battle', $id)->get();
    }

    public function start($defense,$planet,$travel) {
        $playerOwnerPlatet = $this->playerService->iSplayerOwnerPlanet($defense,$planet);

        if($playerOwnerPlatet){

            $attack  = Player::getPlayerLogged();
            $defense = Player::find($defense);

            $aUnits  = $this->travelService->getTroopAttack($travel);
            $dUnits  = $this->travelService->getTroopDefense($travel);

            $aStrategy = $attack->attackStrategy;
            $dStrategy = $defense->defenseStrategy;

            return $this->battleService->startNewBattle (
                $attack->id,
                $defense->id,
                $aUnits,
                $dUnits,
                $aStrategy,
                $dStrategy,
                $planet
            );
        }
        else{
            return response()->json([
                'message' => 'The player is not the owner of the planet'
            ], Response::HTTP_FORBIDDEN);
        }
    }
}
