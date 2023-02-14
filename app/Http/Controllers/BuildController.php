<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Services\BuildService;

use Illuminate\Http\Request;

class BuildController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function list(Request $request)
    {
        return Building::where("planet", $request->planetId);
    }

    public function availables(Request $request)
    {
        $buildSerivice = new BuildService();
        return $buildSerivice->listAvailableBuilds($request->planetId);
    }

    public function plant(Request $request) {
        $building = new Building();
        $building->build = $request->build;
        $building->planet = $request->planetId;
        $building->slot = $request->slot;

        $buildSerivice = new BuildService();
        $buildService->plant($building);
    }

    public function upgrade(Request $request) {
        $buildSerivice = new BuildService();
        $buildService->upgrade($request->buildingId);
    }

    public function worker(Request $request) {
        $buildSerivice = new BuildService();
        $buildService->configWorkers(
            $request->planetId,
            $request->workers,
            $request->buildingId
        );
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
