<?php

namespace App\Http\Controllers;

class ChallangeController extends Controller
{
    public function startMission ()
    {
        // $player = Player::getPlayerLogged();

        // if (!$player) {
        //     return response()->json(['error' => 'Unauthenticated player.'], Response::HTTP_UNAUTHORIZED);
        // }

        // $mission = Mission::where([['player','=', $player->id],['status', '=', 'pending']])->orderBy('date', 'desc')->limit(1)->get();

        // return response()->json($mission);
    }
}
