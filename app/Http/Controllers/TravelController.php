<?php

namespace App\Http\Controllers;

use App\Models\Travel;
use App\Services\TravelService;
use Illuminate\Http\Request;
use App\Models\Player;
use Illuminate\Http\Response;

class TravelController extends Controller
{
    private $itensPerPage = 10;

    protected $travelService;

    public function __construct(TravelService $travelService)
    {
        $this->travelService = $travelService;
    }

    /**
     * @OA\Get(
     *     path="/travel/list",
     *     operationId="listTravel",
     *     tags={"Travel"},
     *     summary="List travels",
     *     description="Get a paginated list of travels for the logged-in player",
     *     @OA\Response(response="200", description="Successful operation"),
     *     @OA\Response(response="401", description="Unauthorized"),
     *     @OA\Response(response="500", description="Internal server error"),
     * )
     * @return mixed
     */
    public function list() {
        try {
            $player = Player::getPlayerLogged();
            if (!$player) {
                return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
            }

            $travels = Travel::where('player', $player->id)->orderBy('arrival')->paginate($this->itensPerPage);
            return response()->json($travels, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *     path="/travel/current",
     *     operationId="getCurrentTravel",
     *     tags={"Travel"},
     *     summary="Get current travel",
     *     description="Get the current travel for the logged-in player",
     *     @OA\Response(response="200", description="Successful operation"),
     *     @OA\Response(response="401", description="Unauthorized"),
     *     @OA\Response(response="500", description="Internal server error"),
     * )
     * @return mixed
     */
    public function current() {
        try {
            $player = Player::getPlayerLogged();
            if (!$player) {
                return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
            }

            $currentTravel = $this->travelService->getCurrent($player->id);

            return response()->json($currentTravel, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function missions($action) {
        try {
            $player = Player::getPlayerLogged();
            if (!$player) {
                return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
            }

            $missions = $this->travelService->getMissions($action);

            return response()->json($missions, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            return response()->json(['message' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     *
     * @OA\Post(
     *     path="/travel/start",
     *     operationId="startTravel",
     *     tags={"Travel"},
     *     summary="Start travel",
     *     description="Start a new travel for the logged-in player",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="property1", type="string", example="value1"),
     *                 @OA\Property(property="property2", type="integer", example=123)
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Successful operation"),
     *     @OA\Response(response="401", description="Unauthorized"),
     *     @OA\Response(response="500", description="Internal server error"),
     * )
     * @param Request $request
     * @return string|null
     */
    public function start (Request $request) {

        try {
            $player = Player::getPlayerLogged();
            if (!$player) {
                return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
            }

            $requestData = $request->all();
            $result = $this->travelService->start($player->id, $requestData);

            return response()->json($result, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            return response()->json(['message' => $e->getTraceAsString()], Response::HTTP_INTERNAL_SERVER_ERROR);
            return response()->json(['message' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     *
     * @OA\Post(
     *     path="/travel/back/{travel}",
     *     operationId="backTravel",
     *     tags={"Travel"},
     *     summary="Go back in travel",
     *     description="Go back in a specific travel for the logged-in player",
     *     @OA\Parameter(
     *         name="travel",
     *         in="path",
     *         description="ID of the travel",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(response="200", description="Successful operation"),
     *     @OA\Response(response="401", description="Unauthorized"),
     *     @OA\Response(response="500", description="Internal server error"),
     * )
     * @param $travel
     * @return void
     */
    public function back ($travel) {
        try {
            $player = Player::getPlayerLogged();
            if (!$player) {
                return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
            }

            $currentTravel = Travel::where([
                ['player', $player->id],
                ['id', $travel]
            ])->first();

            if ($currentTravel) {
                $this->travelService->back($travel);
            }

            return response()->json(['message' => 'Success'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *     path="/travel/list-status/{status}",
     *     operationId="listTravel",
     *     tags={"Travel"},
     *     summary="List travels",
     *     description="Get travels by status the logged-in player",
     *     @OA\Response(response="200", description="Successful operation"),
     *     @OA\Response(response="401", description="Unauthorized"),
     *     @OA\Response(response="500", description="Internal server error"),
     * )
     * @return mixed
     */
    public function listStatus(Request $request) {
        try {
            $player = Player::getPlayerLogged();
            if (!$player) {
                return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
            }

            $status = $request->input('status');
            $travels = Travel::where('player', $player->id)->where('status', $status)->orderBy('id')->get();

            return response()->json($travels, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
