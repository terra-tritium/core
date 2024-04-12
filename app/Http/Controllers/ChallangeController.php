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
        $result = $challangeService->startMission($player->id, $from, $to);

        if (!$result) {
            return response()->json(['error' => 'Exist current mission'], Response::HTTP_ACCEPTED);
        }

        return response()->json("Success");
    }

    public function podium () {
        $challangeService = new ChallangeService();
        $podium = $challangeService->podium();

        return response()->json($podium);
    }

    public function mission ($planet) {
        $player = Player::getPlayerLogged();

        if (!$player) {
            return response()->json(['error' => 'Unauthenticated player.'], Response::HTTP_UNAUTHORIZED);
        }

        $challangeService = new ChallangeService();
        $missions = $challangeService->mission($player->id, $planet);

        return response()->json($missions);
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
