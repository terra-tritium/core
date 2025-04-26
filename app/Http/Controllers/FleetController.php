<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Services\FleetService;
use Illuminate\Http\Request;
use App\Services\PlayerService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class FleetController extends Controller
{

    protected $fleetService;
    protected $playerService;

    public function __construct(FleetService $fleetService, PlayerService $playerService)
    {
        $this->fleetService = $fleetService;
        $this->playerService = $playerService;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Production  $player
     * @return \Illuminate\Http\Response
     *
     * @OA\Post (
     *     path="/api/fleet/production/{planet}",
     *     tags={"Fleet"},
     *     summary="Produce Fleets",
     *     security={
     *         {"bearerAuthFleetP": {}}
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
     *     securityScheme="bearerAuthFleetP"
     * )
     *
     */

    public function production(Request $request, $planet) {
        $player = Player::getPlayerLogged();

        try {
            $player = Player::getPlayerLogged();

            $unit = $request->all();

            if ($unit["quantity"] <= 0) {
                return response()->json(['message' => 'Quantity must be greater than 0.'],
                    Response::HTTP_BAD_REQUEST);
            }

            if ($this->playerService->isPlayerOwnerPlanet($player->id, $planet)) {
                return $this->fleetService->production($player->id, $planet, $unit);
            } else {
                return response()->json(['message' => 'You are not authorized to perform this action.'],
                    Response::HTTP_FORBIDDEN);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'An error occurred during Fleet production.', 'err' => $exception->getMessage()],
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
     *     path="/api/fleet/production/{planet?}",
     *     tags={"Fleet"},
     *     summary="List Fleets in Producions",
     *     security={
     *         {"bearerAuthFleet": {}}
     *     },
     *     @OA\Response(response="200", description="Resposta bem-sucedida")
     * )
     *  @OA\SecurityScheme(
     *     type="http",
     *     scheme="bearer",
     *     securityScheme="bearerAuthFleet"
     * )
     *
     */

    public function producing(Request $request){
        try {
            $player = Player::getPlayerLogged();
            $planet = $request->planet;
            if ($this->playerService->isPlayerOwnerPlanet($player->id, $planet)) {
                return $this->fleetService->productionFleet($player, $planet);
            } else {
                return response()->json(['message' => 'You are not authorized to perform this action.'],
                    Response::HTTP_FORBIDDEN);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'An error occurred during Fleet production.'],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Fleet  $player
     * @return \Illuminate\Http\Response
     *
     * @OA\Get (
     *     path="/api/Fleet/{planet}",
     *     tags={"Fleet"},
     *     summary="List Fleets of planet",
     *     security={
     *         {"bearerAuthFleetList": {}}
     *     },
     *     @OA\Response(response="200", description="Resposta bem-sucedida")
     * )
     *  @OA\SecurityScheme(
     *     type="http",
     *     scheme="bearer",
     *     securityScheme="bearerAuthFleetList"
     * )
     *
     */

    public function list($planet){
        try {
            $player = Player::getPlayerLogged();

            if ($this->playerService->isPlayerOwnerPlanet($player->id, $planet)) {
                return $this->fleetService->fleets($player->id, $planet);
            } else {
                # verifica se possui naves estacionadas no planeta que nao eh seu
                return $this->fleetService->listExternalFleets($player->id, $planet);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'An error occurred during list Fleets.'],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function return($planet){
        try {
            $player = Player::getPlayerLogged();

            $travel = $this->fleetService->return($player->id, $planet);

            if ($travel) {
                return response()->json(['message' => 'ok'],
                    Response::HTTP_OK); 
            } else {
                return response()->json(['message' => 'An error occurred during return Fleets from defense.'],
                Response::HTTP_BAD_REQUEST);
            }

        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'An error occurred during list Fleets.'],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
