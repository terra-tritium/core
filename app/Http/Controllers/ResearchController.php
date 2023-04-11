<?php

namespace App\Http\Controllers;

use App\Models\Research;
use App\Models\Researched;
use App\Services\ResearchService;
use Illuminate\Http\Request;

class ResearchController extends Controller
{

    protected $researchService;

    public function __construct(ResearchService $researchService)
    {
        $this->researchService = $researchService;
    }

    public function list() {
        return Research::orderBy('code')->get();
    }

    public function researched() {
        $player = Player::getPlayerLogged();
        return Researched::where("player", $player->id)->get();
    }

    public function start($code) {
        $player = Player::getPlayerLogged();
        return $this->researchService->start($player->id, $code);
    }

    public function done($code) {
        $player = Player::getPlayerLogged();
        return $this->researchService->done($player->id, $code);
    }
}
