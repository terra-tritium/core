<?php

namespace App\Http\Controllers;

//use App\Models\AttackMode;
//use App\Models\DefenseMode;
use App\Models\Player;
use App\Models\Combat;
use App\Models\CombatStage;
use App\Models\Planet;
use App\Models\Strategy;
use App\Services\CombatService;
use App\Services\SpaceCombatService;
use App\Services\PlayerService;
use App\Services\ResourceService;
use App\Services\TravelService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Planet\PlanetResource;

class CombatController extends Controller
{

    public function __construct(
        protected readonly CombatService $combatService,
        protected readonly SpaceCombatService $spaceCombatService,
        protected readonly PlayerService $playerService, 
        protected readonly TravelService $travelService,
        protected readonly ResourceService $resourceService
        )
    {}

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
        return Combat::find($id);
    }

    public function current($combat) {
        $player = Player::getPlayerLogged();

        if (!$player) {
            return response()->json(['error' => 'Unauthenticated player.'], Response::HTTP_UNAUTHORIZED);
        }

        $combat = Combat::where('id', $combat)->firstOrFail();

        return response()->json($combat);
    }

    public function list()
    {
        $player = Player::getPlayerLogged();

        if (!$player) {
            return response()->json(['error' => 'Unauthenticated player.'], Response::HTTP_UNAUTHORIZED);
        }

        $combats = DB::table('combats as c')
            ->join('fighters as f', 'f.combat', '=', 'c.id')
            ->leftJoin('planets as p', 'c.planet', '=', 'p.id')
            ->leftJoin('players as u', 'p.player', '=', 'u.id')
            ->where('f.player', $player->id)
            ->select(
                'c.id',
                'c.planet',
                'c.status',
                'p.name as planetName',
                'p.quadrant',
                'p.position',
                'p.region',
                'u.name as player'
            )
            ->orderByDesc('c.id')
            ->limit(20)
            ->get();

        return response()->json($combats);
    }


    public function figthers($combatId)
    {
        $fighters = DB::table('fighters as f')
            ->join('players as p', 'f.player', '=', 'p.id')
            ->where('f.combat', $combatId)
            ->select('f.*', 'p.name as player', 'p.id as playerId')
            ->get();

        $fightersAlien = DB::table('fighters as f')
            ->where('f.combat', $combatId)
            ->select('f.*')
            ->get();

        if ($fightersAlien) {
            foreach($fightersAlien as $fAlien){
                $fighters->push($fAlien);
            }

            $fighters = $fighters->merge($fightersAlien)->unique('id')->sortByDesc('id')->values();
        }

        return response()->json($fighters);
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
        return response()->json(['count' => $count, "allowed" => $count < config("app.tritium_max_planet_player")], Response::HTTP_OK);
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
        if (count($planets) > config("app.tritium_max_planet_player")) {
            return response()->json(['error' => 'planet limit exceeded.'], Response::HTTP_NOT_FOUND);
        }
        // Planet::where('id', $planet)->update(['player' => $player->id, 'defenseStrategy' => 7, 'attackStrategy' => 7]);
        try {
            $travel = $this->travelService->colonizePlanet($planet, $player->id);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
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
    public function availableResources($planet)
    {
        $player = Player::getPlayerLogged();

        $planetModel = Planet::where(['id' => $planet,'player' => $player->id])->first();

       if(is_null($planetModel))
       {
            $planetModel =  PlanetResource::make(Planet::findOrFail($planet));
       }

        return response()->json($planetModel, Response::HTTP_OK);
    }
    /**
     * enviar recurso
     */

    public function sendResource(Request $request)
    {
        $player = Player::getPlayerLogged();
        if (!$player) {
            return response()->json(['error' => 'Unauthenticated player.'], Response::HTTP_UNAUTHORIZED);
        }
        return $this->resourceService->sendResources($request);
    }

    public function calculateStage($combatId){
        $stage = $this->combatService->calculateStage($combatId);
        return response()->json($stage, Response::HTTP_OK);
    }

    public function arrivalPlanet($from)
    {
        $log = '';
        $travels = $this->combatService->travelsData($from);
        $finished = $this->combatService->travelsFinished($travels);

        if (count($finished) > 0) {
            $dPlanetId = $finished[0]->to;
            $targetHasShip = $this->combatService->targetHasShip($dPlanetId);
            $targetHasShield = $this->combatService->targetHasShield($dPlanetId);
            $targetHasTroop = $this->combatService->targetHasTroop($dPlanetId);
            if ($targetHasShip) {
                //inicio da batalha espacial
                $log = " Inicio da batalha espacial ";

            } else {
                if ($targetHasShield) {
                    if (count($targetHasTroop) > 0) {
                        $log = "O alvo tem escudo, tem tropa, inicio de uma batalha ";
                        $attack = Planet::find($from);
                        $defense = Planet::find($dPlanetId);

                        $log .= $this->combatService->startNewCombat($attack->player, $defense->player,
                            json_decode($finished[0]->troop), $targetHasTroop,$attack->attackStrategy, $defense->defenseStrategy,$dPlanetId);

                    } else {
                        $log = "tem escudo, mas nao tem tropa, capturar recurso ";
                        $log .= $this->combatService->capturarRecurso($finished[0]->id,$dPlanetId, $from);
                    }
                } else {
                    $log = "O alvo não tem escudo, capturar recurs o";
                    $log .= $this->combatService->capturarRecurso($finished[0]->id,$dPlanetId, $from);
                }
            }

            return response()->json([
                'origem' => $from,
                'alvo' => $dPlanetId,
                'log' =>  $log,
                "finished" => $finished,
                'targetHasShip' => $targetHasShip,
                'orbita' => $targetHasShip == false ? 'Orbita dominada' : 'inicio a batalha espacial',
                'shield' => $targetHasShield,
                'targetTroops' => $targetHasTroop,
                "verificar chegada ao planeta"
            ], Response::HTTP_OK);
        } else {
            return response()->json([],Response::HTTP_OK);
        }
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
                $this->start(null, null, null);
                break;
            case "defense":
                $this->defense();
                break;
            case "resource":
                // $this->sendResource();
                break;
            default:
                return response()->json(["error" => "mode not found"], Response::HTTP_BAD_REQUEST);
        }
    }

    /*
        Defender
    */
    public function defense()
    {
    }

    /**
     * Executa ação de abandonar o combate
     */
    public function spaceLeave($combatId) {
        $player = Player::getPlayerLogged();
        if (!$player) {
            return response()->json(['error' => 'Unauthenticated player.'], Response::HTTP_UNAUTHORIZED);
        }
        $this->spaceCombatService->leave($combatId, $player);
        return response()->json([], Response::HTTP_OK);
    }


    public function stages($id)
    {
        return CombatStage::where('combat', $id)->orderByDesc('id') ->get();
    }

    public function start($defense, $planet, $travel)
    {
        $playerOwnerPlatet = $this->playerService->isPlayerOwnerPlanet($defense, $planet);

        if ($playerOwnerPlatet) {

            $attack  = Player::getPlayerLogged();
            $defense = Player::find($defense);

            $aUnits  = $this->travelService->getTroopAttack($travel);
            $dUnits  = $this->travelService->getTroopDefense($travel);

            $aStrategy = $attack->attackStrategy;
            $dStrategy = $defense->defenseStrategy;

            return $this->combatService->startNewCombat(
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
