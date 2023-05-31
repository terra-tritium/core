<?php

namespace App\Http\Controllers;

use App\Models\Planet;
use App\Models\Player;
use Illuminate\Http\Request;

class PlanetController extends Controller
{

    public function find ($quadrant, $position) {
        $planet = Planet::where('quadrant',$quadrant)->where('position',$position)->first();

        if (!$planet) {
            return "no result";
        }
        return $planet;
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

    public function show($id)
    {
        return Planet::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Planet  $planet
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Planet $planet)
    {
        $player = Player::getPlayerLogged();

        if ($planet->player !== $player->id) {
            return response()->json(['message' => "You aren't the owner of this planet"], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $planet->name = $request->input('name');
        $planet->save();

        return response()->json(['message' => 'Planet name successfully updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Planet  $planet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Planet $planet)
    {
        //
    }

    public function list() {
        $player = Player::getPlayerLogged();

        return Planet::where('player',$player->id)->get();
    }
}
