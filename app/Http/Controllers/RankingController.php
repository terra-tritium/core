<?php

namespace App\Http\Controllers;

use App\Models\Ranking;
use App\Models\AlianceRanking;
use App\Models\Player;
use App\Services\RankingService;
use App\ValueObjects\RankingCategory;
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
                return Ranking::orderBy(RankingCategory::SCORE, 'DESC')->take(10)->get();
            case "general":
                return Ranking::orderBy(RankingCategory::SCORE, 'DESC')->paginate($this->itensPerPage);
            case "builder":
                return Ranking::orderBy(RankingCategory::BUILD_SCORE, 'DESC')->paginate($this->itensPerPage);
            case "military":
                return Ranking::orderBy(RankingCategory::MILITARY_SCORE, 'DESC')->paginate($this->itensPerPage);
            case "attack":
                return Ranking::orderBy(RankingCategory::ATTACK_SCORE, 'DESC')->paginate($this->itensPerPage);
            case "defense":
                return Ranking::orderBy(RankingCategory::DEFENSE_SCORE, 'DESC')->paginate($this->itensPerPage);
            case "energy":
                return Ranking::orderBy(RankingCategory::ENERGY, 'DESC')->paginate($this->itensPerPage);
        }
    }

    public function aliances($type) {
        $player = Player::getPlayerLogged();
        switch ($type) {
            case "top":
                return AlianceRanking::orderBy(RankingCategory::SCORE, 'DESC')->limit(5);
            case "general":
                return AlianceRanking::orderBy(RankingCategory::SCORE, 'DESC')->paginate($this->itensPerPage);
            case "builder":
                return AlianceRanking::orderBy(RankingCategory::BUILD_SCORE, 'DESC')->paginate($this->itensPerPage);
            case "military":
                return AlianceRanking::orderBy(RankingCategory::MILITARY_SCORE, 'DESC')->paginate($this->itensPerPage);
            case "attack":
                return AlianceRanking::orderBy(RankingCategory::ATTACK_SCORE, 'DESC')->paginate($this->itensPerPage);
            case "defense":
                return AlianceRanking::orderBy(RankingCategory::DEFENSE_SCORE, 'DESC')->paginate($this->itensPerPage);
            case "energy":
                return AlianceRanking::orderBy(RankingCategory::ENERGY, 'DESC')->paginate($this->itensPerPage);
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
