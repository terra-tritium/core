<?php

namespace App\Http\Controllers;

use App\Models\Logbook;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LogController extends Controller
{
    public function logs()
    {
        $player = Player::getPlayerLogged();

        if (!$player) {
            return response()->json(['error' => 'Unauthenticated player.'], Response::HTTP_UNAUTHORIZED);
        }

        $logs = Logbook::where('player', $player->id)->orderBy('date', 'desc')->limit(100)->get();

        return response()->json($logs);
    }

    public function create(Request $request) {
        $player = Player::getPlayerLogged();

        if (!$player) {
            return response()->json(['error' => 'Unauthenticated player.'], Response::HTTP_UNAUTHORIZED);
        }

        $log = new Logbook();
        $log->player = $player->id;
        $log->text = $request->input('text');
        $log->type = $request->input('type');
        $log->save();

        return response()->json($log);
    }
}
