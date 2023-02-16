<?php

namespace App\Http\Controllers;

use App\Models\Build;
use App\Models\Building;
use App\Services\BuildService;

use Illuminate\Http\Request;

class BuildController extends Controller
{

    protected $buildService;

    public function __construct(BuildService $buildService) {
        $this->buildService = $buildService;
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
        return $this->buildService->configWorkers (
            $request->input("planetId"),
            $request->input("workers"),
            $request->input("buildingId")
        );
    }
}
