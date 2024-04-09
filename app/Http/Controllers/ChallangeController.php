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

        $planetOrigin = Planet::where([['id', '=', $from], ['player', '=', $player->id]])->firstOrFail();

        if (!$planetOrigin) {
            return response()->json(['error' => 'Planet not found for player'], Response::HTTP_NOT_FOUND);
        }

        $challangeService = new ChallangeService();
        $challangeService->startMission($player->id, $from, $to);

        return response()->json("Success");
    }

    public function convert($planet) {
        $player = Player::getPlayerLogged();

        if (!$player) {
            return response()->json(['error' => 'Unauthenticated player.'], Response::HTTP_UNAUTHORIZED);
        }

        $challangeService = new ChallangeService();
        $challangeService->convert($player->id, $planet);

        return response()->json("Success");
    }
}
