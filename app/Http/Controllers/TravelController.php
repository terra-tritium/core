<?php

namespace App\Http\Controllers;

use App\Models\Travel;
use App\Services\TravelService;
use Illuminate\Http\Request;
use App\Models\Player;

class TravelController extends Controller
{
    private $itensPerPage = 10;

    protected $travelService;

    public function __construct(TravelService $travelService)
    {
        $this->travelService = $travelService;
    }

    public function list() {
        $player = Player::getPlayerLogged();
        return Travel::where("player", $player->id)->orderBy('arrival')->paginate($this->itensPerPage);
    }

    public function current() {
        $player = Player::getPlayerLogged();
        return Travel::where([["player", $player->id], ["status", 1]])->orderBy('arrival')->get();
    }

    public function start (Request $request) {
        $player = Player::getPlayerLogged();
        return $this->travelService->start($player->id, $request);
    }

    public function back ($travel) {
        $player = Player::getPlayerLogged();
        $currentTravel = Travel::where([["player", $player->id], ["id", $travel]])->get();
        if ($currentTravel) {
            $this->travelService->back($travel);
        }
    }
}
