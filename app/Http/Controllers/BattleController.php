<?php

namespace App\Http\Controllers;

//use App\Models\AttackMode;
//use App\Models\DefenseMode;
use App\Models\Player;
use App\Models\Battle;
use App\Models\Fighters;
use App\Models\BattleStage;
use App\Models\Planet;
use App\Models\Strategy;
use App\Services\BattleService;
use App\Services\PlayerService;
use App\Services\StrategyService;
use App\Services\TravelService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class BattleController extends Controller
{
    protected $battleService;
    protected $playerService;
    protected $travelService;

    public function __construct(BattleService $battleService, PlayerService $playerService, TravelService $travelService)
    {
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

    public function changeAttackMode($option)
    {
        $user = auth()->user()->id;
        $player = Player::where("user", $user)->firstOrFail();
        $player->attackMode = $option;
        $player->save();
    }

    public function changeDefenseMode($option)
    {
        $user = auth()->user()->id;
        $player = Player::where("user", $user)->firstOrFail();
        $player->defenseMode = $option;
        $player->save();
    }

    public function view($id)
    {
        return Battle::find($id);
    }

    public function list()
    {
        $player = Player::getPlayerLogged();

        if (!$player) {
            return response()->json(['error' => 'Unauthenticated player.'], Response::HTTP_UNAUTHORIZED);
        }

        $battles = Fighters::with('battle', 'planet')->where('player', $player->id)->orderBy('start', 'desc')->limit(12)->get();

        return response()->json($battles);
    }


    public function listStrategy()
    {
        $player = Player::getPlayerLogged();

        if (!$player) {
            return response()->json(['error' => 'Unauthenticated player.'], Response::HTTP_UNAUTHORIZED);
        }
        $strategies = Strategy::all();
        return response()->json($strategies);
    }
    public function strategiesSelectedPlanet($planet)
    {
        $player = Player::getPlayerLogged();

        if (!$player) {
            return response()->json(['error' => 'Unauthenticated player.'], Response::HTTP_UNAUTHORIZED);
        }
        $planet = Planet::select('id', 'attackStrategy', 'defenseStrategy')->find($planet);
        return response()->json($planet);
    }
    public function changeStrategy($planet, $type, $newStrategy)
    {
        $player = Player::getPlayerLogged();
        if (!$player) {
            return response()->json(['error' => 'Unauthenticated player.'], Response::HTTP_UNAUTHORIZED);
        }
        if ($type == 'attack') {
            Planet::where('id', $planet)->update(['attackStrategy' => $newStrategy]);
        } else {
            Planet::where('id', $planet)->update(['defenseStrategy' => $newStrategy]);
        }
        return response()->json([], Response::HTTP_ACCEPTED);
    }
    /**
     * Verifica se o jogador possui a capacidade máxima de planetas
     */
    public function checkNumberOfPlanets()
    {
        $player = Player::getPlayerLogged();
        if (!$player) {
            return response()->json(['error' => 'Unauthenticated player.'], Response::HTTP_UNAUTHORIZED);
        }
        $planets = Planet::where('player', $player->id)->get();
        $count = count($planets);
        return response()->json(['count' => $count, "allowed" => $count < env("MAX_PLANET_PLAYER")], Response::HTTP_OK);
    }
    /**
     * Atribuir novo planeta ao jogador
     */
    public function colonizePlanet($planet)
    {

        $player = Player::getPlayerLogged();
        if (!$player) {
            return response()->json(['error' => 'Unauthenticated player.'], Response::HTTP_UNAUTHORIZED);
        }
        $planets = Planet::where('player', $player->id)->get();
        if (count($planets) > env("MAX_PLANET_PLAYER")) {
            return response()->json(['error' => 'planet limit exceeded.'], Response::HTTP_NOT_FOUND);
        }
        Planet::where('id', $planet)->update(['player' => $player->id]);
        return response()->json([], Response::HTTP_OK);
    }
    /**
     * Verifica se tem nave disponível
     */
    public function availableShip()
    {
        $player = Player::getPlayerLogged();
        if (!$player) {
            return response()->json(['error' => 'Unauthenticated player.'], Response::HTTP_UNAUTHORIZED);
        }
        $ships = 0;
        return response()->json(["ships" => $ships], Response::HTTP_OK);
    }
    public function availableResources($planet){
        $planet = Planet::findOrFail($planet);
        return response()->json($planet, Response::HTTP_OK);
    }
    /**
     * Action mode
     * -attack
     * -defense
     * -resource
     */
    public function actionMode(Request $request)
    {
        $mode = $request->input("mode");
        switch ($mode) {
            case "attack":
                $this->start(null,null,null);
                break;
            case "defense":
                $this->defense();
                break;
            case "resource":
                $this->sendResource();
                break;
            default:
                return response()->json(["error" => "mode not found"], Response::HTTP_BAD_REQUEST);
        }
    }

    /*
        Defender
    */
    public function defense(){

    }
    /**
     * enviar recurso
     */
    public function sendResource(){

    }

    public function stages($id)
    {
        return BattleStage::where('battle', $id)->get();
    }

    public function start($defense, $planet, $travel)
    {
        $playerOwnerPlatet = $this->playerService->iSplayerOwnerPlanet($defense, $planet);

        if ($playerOwnerPlatet) {

            $attack  = Player::getPlayerLogged();
            $defense = Player::find($defense);

            $aUnits  = $this->travelService->getTroopAttack($travel);
            $dUnits  = $this->travelService->getTroopDefense($travel);

            $aStrategy = $attack->attackStrategy;
            $dStrategy = $defense->defenseStrategy;

            return $this->battleService->startNewBattle(
                $attack->id,
                $defense->id,
                $aUnits,
                $dUnits,
                $aStrategy,
                $dStrategy,
                $planet
            );
        } else {
            return response()->json([
                'message' => 'The player is not the owner of the planet'
            ], Response::HTTP_FORBIDDEN);
        }
    }
}
