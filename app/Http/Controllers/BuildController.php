<?php

namespace App\Http\Controllers;

use App\Models\Build;
use App\Models\Building;
use App\Models\Planet;
use App\Models\Player;
use App\Services\BuildService;
use App\Services\WorkerService;

use http\Exception\InvalidArgumentException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class BuildController extends Controller
{

    protected $buildService;
    protected $workerService;

    public function __construct(BuildService $buildService, WorkerService $workerService) {
        $this->buildService = $buildService;
        $this->workerService = $workerService;
    }

    /**
     *
     * @OA\Get(
     *     path="/build/list",
     *     tags={"Build"},
     *     summary="List all builds",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Build")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Internal server error"
     *             )
     *         )
     *     )
     * )
     *
     * @return mixed
     */
    public function list()
    {
        try {
            $builds = Build::orderBy('code')->get();

            return response()->json($builds, Response::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }    }

    /**
     *
     * * @OA\Get(
     *     path="/build/availables/{planet}",
     *     tags={"Build"},
     *     summary="Get available builds for a planet",
     *     @OA\Parameter(
     *         name="planet",
     *         in="path",
     *         required=true,
     *         description="ID or name of the planet",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Build")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthorized"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Internal server error"
     *             )
     *         )
     *     )
     * )
     *
     * @param $planet
     * @return array
     */
    public function availables($planet)
    {
        try {
            $builds = $this->buildService->listAvailableBuilds($planet);

            return response()->json($builds, Response::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }    }

    /**
     *
     * * @OA\Get(
     *     path="/building/list/{planet}",
     *     tags={"Building"},
     *     summary="Get list of buildings for a planet",
     *     @OA\Parameter(
     *         name="planet",
     *         in="path",
     *         required=true,
     *         description="ID or name of the planet",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Building")
     *         )
     *     ),
     *    @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Internal server error"
     *             )
     *         )
     *     )
     * )
     *
     * @param $planet
     * @return mixed
     */
    public function listBildings($planet) {
        try {
            $buildings = $this->buildService->listBuildings($planet);

            return response()->json($buildings, Response::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }    }

    /**
     *
     * @OA\Post(
     *     path="/build/plant",
     *     tags={"Build"},
     *     summary="Plant a building",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="build", type="string", description="Building type"),
     *             @OA\Property(property="planet", type="integer", description="Planet ID"),
     *             @OA\Property(property="slot", type="integer", description="Slot number")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Building planted successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     *
     * @param Request $request
     * @return void
     */
    public function plant(Request $request) {
        try {
            $building = app(Building::class);
            $building->build = $request->input("build");
            $building->planet = $request->input("planet");
            $building->slot = $request->input("slot");
            
            $playerLogged = Player::getPlayerLogged();
            $planet = Planet::where('player', $playerLogged->id)
                ->where('id', $building->planet)
                ->get();
            if(count($planet) == 0){
                return response()->json(
                ['message' => "You aren't the owner of this planet"],
                Response::HTTP_FORBIDDEN);
            }    

            $this->buildService->plant($building);

            return response()->json(['message' => 'Building planted successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     *
     * @OA\Post(
     *     path="/build/up",
     *     tags={"Build"},
     *     summary="Upgrade a building",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", description="Building ID")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Building upgrade successful"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid building ID"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error upgrading building"
     *     )
     * )
     *
     * @param Request $request
     * @return void
     */
    public function upgrade(Request $request) {
        $buildingId = $request->input("id");
    
       
        try {
            $playerLogged = Player::getPlayerLogged();
            $planet = Planet::where('player', $playerLogged->id)
                ->where('id', $request->input("planet"))
                ->get();
            if(count($planet) == 0){
                return response()->json(
                ['message' => "You aren't the owner of this planet"],
                Response::HTTP_FORBIDDEN);
            }    

            $this->buildService->upgrade($buildingId);
            return response()->json(['message' => 'Building upgrade successful'], Response::HTTP_OK);
        } catch (InvalidArgumentException $exception) {
            return response()->json(['message' => 'Invalid building ID'], Response::HTTP_BAD_REQUEST);
        } catch (Throwable $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Error upgrading building'],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *     path="/build/demolish",
     *     tags={"Build"},
     *     summary="Demolish a building",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", description="Building ID")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Building demolish successful"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid building ID"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error demolishing building"
     *     )
     * )
     */
    public function demolish(Request $request) {
        try {

            $playerLogged = Player::getPlayerLogged();
            $planet = Planet::where('player', $playerLogged->id)
                ->where('id', $request->input("planet"))
                ->get();
            if(count($planet) == 0){
                return response()->json(
                ['message' => "You aren't the owner of this planet"],
                Response::HTTP_FORBIDDEN);
            }    
            $build = $request->input("build");
            $planetId = $request->input("planet");

             $this->buildService->demolish($build,$planetId);

            return response()->json(['message' => 'Build demolish successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal server error', $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param $build
     * @return \Illuminate\Http\JsonResponse
     */
    public function requires($build) {
        try {
            $requiredResources = $this->buildService->requires($build);
            return response()->json(['data' => $requiredResources], Response::HTTP_OK);
        } catch (InvalidArgumentException $exception) {
            return response()->json(['message' => 'Invalid building type'], Response::HTTP_BAD_REQUEST);
        } catch (Throwable $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Error getting required resources'],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param $build
     * @param $level
     * @return \Illuminate\Http\JsonResponse
     */
    public function require($build, $level) {
        try {
            $result = $this->buildService->require($build, $level);
            return response()->json($result, Response::HTTP_OK);
        } catch (\Throwable $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Error: Failed to retrieve requirements'],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *     path="/build/workers",
     *     tags={"Build"},
     *     summary="Configure workers for a building",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Request body",
     *         @OA\JsonContent(
     *             @OA\Property(property="planetId", type="integer", description="ID of the planet"),
     *             @OA\Property(property="workers", type="integer", description="Number of workers"),
     *             @OA\Property(property="buildingId", type="integer", description="ID of the building")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Workers configured successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Workers configured successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error configuring workers",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error configuring workers")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @return string|null
     */
    public function workers (Request $request) {
        try {
            $planetId = $request->input("planetId");
            $workers = $request->input("workers");
            $buildingId = $request->input("buildingId");

            $this->workerService->configWorkers($planetId, $workers, $buildingId);

            return response()->json(['message' => 'Workers configured successfully'], Response::HTTP_OK);
        } catch (Throwable $exception) {
            Log::error($exception);

            return response()->json(['message' => 'Error configuring workers'],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
