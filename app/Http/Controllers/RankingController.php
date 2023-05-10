<?php

namespace App\Http\Controllers;

use App\Models\Ranking;
use App\Models\Aliance;
use App\Models\Player;
use App\Services\RankingService;
use Illuminate\Http\Request;

class RankingController extends Controller
{

    private $itensPerPage = 100;

    private $rankingService;

    public function __construct(RankingService $rankingService)
    {
        $this->rankingService = $rankingService;
    }

    public function players($type) {
        $player = Player::getPlayerLogged();
        switch ($type) {
            case "top":
                return Ranking::orderBy('score', 'DESC')->take(10)->get();
            case "general":
                return Ranking::orderBy('score', 'DESC')->paginate($this->itensPerPage);
            case "builder":
                return Ranking::orderBy('buildScore', 'DESC')->paginate($this->itensPerPage);
            case "military":
                return Ranking::orderBy('militaryScore', 'DESC')->paginate($this->itensPerPage);
            case "attack":
                return Ranking::orderBy('attackScore', 'DESC')->paginate($this->itensPerPage);
            case "defense":
                return Ranking::orderBy('defenseScore', 'DESC')->paginate($this->itensPerPage);
            case "energy":
                return Ranking::orderBy('energy', 'DESC')->paginate($this->itensPerPage);
        }
    }

    public function aliances($type) {
        $player = Player::getPlayerLogged();
        switch ($type) {
            case "top":
                return Aliance::orderBy('score', 'DESC')->limit(5);
            case "general":
                return Aliance::orderBy('score', 'DESC')->paginate($this->itensPerPage);
            case "builder":
                return Aliance::orderBy('buildScore', 'DESC')->paginate($this->itensPerPage);
            case "military":
                return Aliance::orderBy('militaryScore', 'DESC')->paginate($this->itensPerPage);
            case "attack":
                return Aliance::orderBy('attackScore', 'DESC')->paginate($this->itensPerPage);
            case "defense":
                return Aliance::orderBy('defenseScore', 'DESC')->paginate($this->itensPerPage);
            case "energy":
                return Aliance::orderBy('energy', 'DESC')->paginate($this->itensPerPage);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPlayerRanking(Request $request)
    {
        $name = $request->input('name');
        $orderBy = $request->input('orderBy');

        $rankings = $this->rankingService->getRankings($name, $orderBy);

        return response()->json($rankings);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAlianceRanking(Request $request)
    {
        $name = $request->input('name');
        $orderBy = $request->input('orderBy');

        $aliances = $this->rankingService->getAlianceRankings($name, $orderBy);

        return response()->json($aliances);
    }
}
