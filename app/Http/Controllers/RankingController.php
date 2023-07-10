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
        
            $type = $request->input('type', 'general');
            $player = Player::getPlayerLogged();
            
            switch ($type) {
                case "top":
                    $query = Ranking::orderBy(RankingCategory::SCORE, 'DESC')->take(4);
                    break;
                case "general":
                    $query = Ranking::orderBy(RankingCategory::SCORE, 'DESC');
                    break;
                case "builder":
                    $query = Ranking::orderBy(RankingCategory::BUILD_SCORE, 'DESC');
                    break;
                case "military":
                    $query = Ranking::orderBy(RankingCategory::MILITARY_SCORE, 'DESC');
                    break;
                case "attack":
                    $query = Ranking::orderBy(RankingCategory::ATTACK_SCORE, 'DESC');
                    break;
                case "defense":
                    $query = Ranking::orderBy(RankingCategory::DEFENSE_SCORE, 'DESC');
                    break;
                case "energy":
                    $query = Ranking::orderBy(RankingCategory::ENERGY, 'DESC');
                    break;
                default:
                    return response()->json(['error' => 'Invalid ranking type'], 400);
            }
    
            $rankings = $query->skip(0)->take($this->itemsPerPage)->get();
            return response()->json($rankings);
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
