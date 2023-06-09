<?php

namespace App\Http\Controllers;

use App\Models\Troop;
use App\Models\Player;
use App\Models\Production;
use App\Services\TroopService;
use Illuminate\Http\Request;
use App\Services\PlayerService;
use Illuminate\Support\Facades\Log;

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
     *     tags={"Troop"},
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

        try {
            $player = Player::getPlayerLogged();

            if ($this->playerService->isPlayerOwnerPlanet($player->id, $planet)) {
                return $this->troopService->production($player->id, $planet, $request->all());
            } else {
                return response()->json(['message' => 'You are not authorized to perform this action.'], 403);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'An error occurred during troop production.'], 500);
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
     *     tags={"Troop"},
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
        try {
            $player = Player::getPlayerLogged();
            $planet = $request->planet;
            if ($this->playerService->isPlayerOwnerPlanet($player->id, $planet)) {
                return $this->troopService->productionTroop($player, $planet);
            } else {
                return response()->json(['message' => 'You are not authorized to perform this action.'], 403);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'An error occurred during troop production.'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Troop  $player
     * @return \Illuminate\Http\Response
     *
     * @OA\Get (
     *     path="/api/troop/{planet}",
     *     tags={"Troop"},
     *     summary="List Troops of planet",
     *     security={
     *         {"bearerAuthTroopList": {}}
     *     },
     *     @OA\Response(response="200", description="Resposta bem-sucedida")
     * )
     *  @OA\SecurityScheme(
     *     type="http",
     *     scheme="bearer",
     *     securityScheme="bearerAuthTroopList"
     * )
     *
     */

    public function list($planet){
        try {
            $player = Player::getPlayerLogged();

            if ($this->playerService->isPlayerOwnerPlanet($player->id, $planet)) {
                return $this->troopService->troops($player->id, $planet);
            } else {
                return response()->json(['message' => 'You are not authorized to perform this action.'], 403);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'An error occurred during list troops.'], 500);
        }
    }
}
