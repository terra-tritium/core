<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Research;
use App\Models\Researched;
use App\Services\ResearchService;
use Illuminate\Http\Request;

class ResearchController extends Controller
{

    protected ResearchService $researchService;

    public function __construct(ResearchService $researchService)
    {
        $this->researchService = $researchService;
    }

    /**
     * @return mixed
     *
     */
    public function list() {
        return Research::orderBy('code')->get();
    }

    /**
     * @return mixed
     */
    public function researched() {
        $player = Player::getPlayerLogged();
        return Researched::where("player", $player->id)->get();
    }

    /**
     * @param int $code
     * @param $sincronize
     * @return string|null
     */
    public function start(int $code, $sincronize = false) {
        $player = Player::getPlayerLogged();
        return $this->researchService->start($player->id, $code, $sincronize);
    }

    /**
     * @param int $code
     * @return null
     *
     */
    public function done(int $code) {
        $player = Player::getPlayerLogged();
        return $this->researchService->done($player->id, $code);
    }

    /**
     * @param int $code
     * @return mixed
     */
    public function getStatus(int $code)
    {
        $player = Player::getPlayerLogged();
        return $this->researchService->status($player->id, $code);
    }
}
