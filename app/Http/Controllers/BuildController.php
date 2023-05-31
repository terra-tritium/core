<?php

namespace App\Http\Controllers;

use App\Models\Build;
use App\Models\Building;
use App\Services\BuildService;
use App\Services\WorkerService;

use Illuminate\Http\Request;

class BuildController extends Controller
{

    protected $buildService;
    protected $workerService;

    public function __construct(BuildService $buildService, WorkerService $workerService) {
        $this->buildService = $buildService;
        $this->workerService = $workerService;
    }

    public function list()
    {
        return Build::orderBy('code')->get();
    }

    public function availables($planet)
    {
        return $this->buildService->listAvailableBuilds($planet);
    }

    public function listBildings($planet) {
        return $this->buildService->listBildings($planet);
    }

    public function plant(Request $request) {
        $building = new Building();
        $building->build = $request->input("build");
        $building->planet = $request->input("planet");
        $building->slot = $request->input("slot");

        $this->buildService->plant($building);
    }

    public function upgrade(Request $request) {
        $this->buildService->upgrade($request->input("id"));
    }

    public function requires($build) {
        return $this->buildService->requires($build);
    }

    public function require($build, $level) {
        return $this->buildService->require($build, $level);
    }

    public function workers (Request $request) {
        return $this->workerService->configWorkers (
            $request->input("planetId"),
            $request->input("workers"),
            $request->input("buildingId")
        );
    }
}
