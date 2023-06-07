<?php

namespace App\Http\Controllers;

use App\Models\Build;
use App\Models\Building;
use App\Services\BuildService;
use App\Services\WorkerService;

use http\Exception\InvalidArgumentException;
use Illuminate\Http\Request;
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
        return Build::orderBy('code')->get();
    }

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
     * )
     *
     * @param $planet
     * @return array
     */
    public function availables($planet)
    {
        return $this->buildService->listAvailableBuilds($planet);
    }

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
     * )
     *
     * @param $planet
     * @return mixed
     */
    public function listBildings($planet) {
        return $this->buildService->listBildings($planet);
    }

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
     *     )
     * )
     *
     * @param Request $request
     * @return void
     */
    public function plant(Request $request) {
        $building = new Building();
        $building->build = $request->input("build");
        $building->planet = $request->input("planet");
        $building->slot = $request->input("slot");

        $this->buildService->plant($building);
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
            $this->buildService->upgrade($buildingId);
            return response()->json(['message' => 'Building upgrade successful'], 200);
        } catch (InvalidArgumentException $exception) {
            return response()->json(['message' => 'Invalid building ID'], 400);
        } catch (Throwable $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Error upgrading building'], 500);
        }
    }

    /**
     * @param $build
     * @return \Illuminate\Http\JsonResponse
     */
    public function requires($build) {
        try {
            $requiredResources = $this->buildService->requires($build);
            return response()->json(['data' => $requiredResources], 200);
        } catch (InvalidArgumentException $exception) {
            return response()->json(['message' => 'Invalid building type'], 400);
        } catch (Throwable $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Error getting required resources'], 500);
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
            return response()->json($result, 200);
        } catch (\Throwable $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Error: Failed to retrieve requirements'], 500);
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

            return response()->json(['message' => 'Workers configured successfully'], 200);
        } catch (Throwable $exception) {
            Log::error($exception);

            return response()->json(['message' => 'Error configuring workers'], 500);
        }
    }
}
