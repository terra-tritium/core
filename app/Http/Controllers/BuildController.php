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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $buildService->upgrade($request->buildingId);
    }

    public function worker(Request $request) {
        $this->buildService->configWorkers(
            $request->planetId,
            $request->workers,
            $request->buildingId
        );
    }

    public function requires($build) {
        $buildService->requires($build);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
