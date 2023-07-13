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

        $logs = Logbook::where('player', $player->id)->get();

        return response()->json($logs);
    }

    public function create() {
        $player = Player::getPlayerLogged();

        if (!$player) {
            return response()->json(['error' => 'Unauthenticated player.'], Response::HTTP_UNAUTHORIZED);
        }

        if (request()->input('type') < 0 || request()->input('type') > 5) {
            return response()->json(['error' => 'Invalid type.'], Response::HTTP_BAD_REQUEST);
        }

        $log = new Logbook();
        $log->player = $player->id;
        $log->message = request()->input('message');
        $lot->type = request()->input('type');
        $log->save();

        return response()->json($log);
    }
}
