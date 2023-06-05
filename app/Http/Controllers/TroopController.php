<?php

namespace App\Http\Controllers;

use App\Models\Troop;
use App\Models\Player;
use App\Models\Production;
use App\Services\TroopService;
use Illuminate\Http\Request;
use App\Services\PlayerService;

class TroopController extends Controller
{

    protected $troopService;
    protected $playerService;

    public function __construct(TroopService $troopService, PlayerService $playerService)
    {
        $this->troopService = $troopService;
        $this->playerService = $playerService;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Production  $player
     * @return \Illuminate\Http\Response
     *
     * @OA\Post (
     *     path="/api/troop/production/{planet}",
     *     tags={"Produce/Troop"},
     *     summary="Produce Troops",
     *     security={
     *         {"bearerAuthTroopP": {}}
     *     },
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"quantity","id"},
     *               @OA\Property(property="quantity", type="integer"),
     *               @OA\Property(property="id", type="integer")
     *            ),
     *        ),
     *     ),
     *     @OA\Parameter(
     *          name="id",
     *          description="Planet id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\Response(response="200", description="Resposta bem-sucedida")
     * )
     *  @OA\SecurityScheme(
     *     type="http",
     *     scheme="bearer",
     *     securityScheme="bearerAuthTroopP"
     * )
     *
     */

    public function production(Request $request, $planet) {
        $player = Player::getPlayerLogged();

        if($this->playerService->iSplayerOwnerPlanet($player->id,$planet)){
            return $this->troopService->production($player->id,$planet, $request->collect());
        }else{
            return ['You are not that smart.'];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Production  $player
     * @return \Illuminate\Http\Response
     *
     * @OA\Get (
     *     path="/api/troop/production/{planet?}",
     *     tags={"Producing/Troop"},
     *     summary="List Troops in Producions",
     *     security={
     *         {"bearerAuthTroop": {}}
     *     },
     *     @OA\Response(response="200", description="Resposta bem-sucedida")
     * )
     *  @OA\SecurityScheme(
     *     type="http",
     *     scheme="bearer",
     *     securityScheme="bearerAuthTroop"
     * )
     *
     */

    public function producing(Request $request){
        $player = Player::getPlayerLogged();
        
        if($this->playerService->iSplayerOwnerPlanet($player->id,$request->planet)){
            return $this->troopService->productionTroop($player,$request->planet);
        }
    }
}
