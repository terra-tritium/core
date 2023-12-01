<?php

namespace App\Http\Controllers;

use App\Models\Shipyard;
use App\Models\Player;
use App\Models\Production;
use App\Services\ShipyardService;
use Illuminate\Http\Request;
use App\Services\PlayerService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ShipyardController extends Controller
{

    protected $shipyardService;
    protected $playerService;

    public function __construct(ShipyardService $shipyardService, PlayerService $playerService)
    {
        $this->shipyardService = $shipyardService;
        $this->playerService = $playerService;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Production  $player
     * @return \Illuminate\Http\Response
     *
     * @OA\Post (
     *     path="/api/shipyard/production/{planet}",
     *     tags={"Shipyard"},
     *     summary="Produce Shipyard",
     *     security={
     *         {"bearerAuthShipyardP": {}}
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
     *     securityScheme="bearerAuthShipyardP"
     * )
     *
     */

    public function production(Request $request, $planet) {
        $player = Player::getPlayerLogged();

        try {
            $player = Player::getPlayerLogged();

            if ($this->playerService->isPlayerOwnerPlanet($player->id, $planet)) {
                return $this->shipyardService->production($player->id, $planet, $request->all());
            } else {
                return response()->json(['message' => 'You are not authorized to perform this action.'],
                    Response::HTTP_FORBIDDEN);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'An error occurred during troop production.'],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Production  $player
     * @return \Illuminate\Http\Response
     *
     * @OA\Get (
     *     path="/api/shipyard/production/{planet?}",
     *     tags={"Shipyard},
     *     summary="List Shipyards in Producions",
     *     security={
     *         {"bearerAuthShipyard": {}}
     *     },
     *     @OA\Response(response="200", description="Resposta bem-sucedida")
     * )
     *  @OA\SecurityScheme(
     *     type="http",
     *     scheme="bearer",
     *     securityScheme="bearerAuthShipyard"
     * )
     *
     */

    public function producing(Request $request){
        try {
            $player = Player::getPlayerLogged();
            $planet = $request->planet;
            if ($this->playerService->isPlayerOwnerPlanet($player->id, $planet)) {
                return $this->shipyardService->productionShipyard($player, $planet);
            } else {
                return response()->json(['message' => 'You are not authorized to perform this action.'],
                    Response::HTTP_FORBIDDEN);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'An error occurred during troop production.'],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Shipyard  $player
     * @return \Illuminate\Http\Response
     *
     * @OA\Get (
     *     path="/api/shipyard/{planet}",
     *     tags={"Shipyard"},
     *     summary="List Shipyards of planet",
     *     security={
     *         {"bearerAuthShipyardList": {}}
     *     },
     *     @OA\Response(response="200", description="Resposta bem-sucedida")
     * )
     *  @OA\SecurityScheme(
     *     type="http",
     *     scheme="bearer",
     *     securityScheme="bearerAuthShipyardList"
     * )
     *
     */

    public function list($planet){
        try {
            $player = Player::getPlayerLogged();

            if ($this->playerService->isPlayerOwnerPlanet($player->id, $planet)) {
                return $this->shipyardService->shipyards($player->id, $planet);
            } else {
                return response()->json(['message' => 'You are not authorized to perform this action.'],
                    Response::HTTP_FORBIDDEN);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'An error occurred during list troops.'],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
