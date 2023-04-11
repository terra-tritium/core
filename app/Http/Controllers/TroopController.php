<?php

namespace App\Http\Controllers;

use App\Models\Troop;
use App\Services\TroopService;
use Illuminate\Http\Request;

class TroopController extends Controller
{

    protected $troopService;

    public function __construct(TroopService $troopService)
    {
        $this->troopService = $troopService;
    }

    public function production(Request $request, $planet) {
        $player = Player::getPlayerLogged();
        return $this->troopService->production($player->id,$planet, $request->collect());
    }
}
