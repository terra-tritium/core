<?php


namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Planet;
use App\Services\ChallangeService;
use Symfony\Component\HttpFoundation\Response;

class ChallangeController extends Controller
{
    public function startMission ($from, $to)
    {
        $player = Player::getPlayerLogged();

        if (!$player) {
            return response()->json(['error' => 'Unauthenticated player.'], Response::HTTP_UNAUTHORIZED);
        }

        Planet::where([['id', '=', $from], ['player', '=', $player->id]])->firstOrFail();

        $challangeService = new ChallangeService();
        $challangeService->startMission($player->id, $from, $to);
    }
}
