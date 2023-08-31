<?php

namespace App\Services;

use App\Models\Aliance;
use App\Models\AlianceMember;
use App\Models\AlianceRanking;
use App\Models\Player;
use App\Models\Ranking;
use App\ValueObjects\RankingCategory;
use GuzzleHttp\Psr7\Response;

class RankingService
{
    public function addPoints($points)
    {
        $logged = Player::getPlayerLogged();
        $player = Player::find($logged->id);
        $player->score += $points;
        $player->save();
    }

    public function getRankings($name = null, $orderBy = null)
    {
        $query = Ranking::query();

        if ($name) {
            $query->whereRaw('name LIKE ?', ['%' . $name . '%']);
        }

        if ($orderBy) {
            switch ($orderBy) {
                case RankingCategory::SCORE:
                    $query->orderBy(RankingCategory::SCORE, 'desc');
                    break;
                case RankingCategory::RESEARCH_SCORE:
                    $query->orderBy(RankingCategory::RESEARCH_SCORE, 'desc');
                    break;
                case RankingCategory::ATTACK_SCORE:
                    $query->orderBy(RankingCategory::ATTACK_SCORE, 'desc');
                    break;
                case RankingCategory::DEFENSE_SCORE:
                    $query->orderBy(RankingCategory::DEFENSE_SCORE, 'desc');
                    break;
                case RankingCategory::BUILD_SCORE:
                    $query->orderBy(RankingCategory::BUILD_SCORE, 'desc');
                    break;
                default:
                    $query->orderBy(RankingCategory::SCORE, 'desc');
                    break;
            }
        }

        return $query->get();
    }

    public function getAlianceRankings($orderBy = null, $filter = null)
    {
        $query = AlianceRanking::query();

        if ($orderBy) {
            switch ($orderBy) {
                case RankingCategory::SCORE:
                    $query->orderBy(RankingCategory::SCORE, 'desc');
                    break;
                case RankingCategory::BUILD_SCORE:
                    $query->orderBy(RankingCategory::BUILD_SCORE, 'desc');
                    break;
                case RankingCategory::LAB_SCORE:
                    $query->orderBy(RankingCategory::LAB_SCORE, 'desc');
                    break;
                case RankingCategory::TRADE_SCORE:
                    $query->orderBy(RankingCategory::TRADE_SCORE, 'desc');
                    break;
                case RankingCategory::ATTACK_SCORE:
                    $query->orderBy(RankingCategory::ATTACK_SCORE, 'desc');
                    break;
                case RankingCategory::DEFENSE_SCORE:
                    $query->orderBy(RankingCategory::DEFENSE_SCORE, 'desc');
                    break;
                case RankingCategory::WAR_SCORE:
                    $query->orderBy(RankingCategory::WAR_SCORE, 'desc');
                    break;
                default:
                    $query->orderBy(RankingCategory::SCORE, 'desc');
                    break;
            }
        }

        if ($filter) {
            $query->where('name', 'like', "%{$filter}%");
        }

        return $query->get();
    }
    public function initScoresAliance()
    {
        $aliances = new Aliance();
        $sumScoreMembers = $aliances->getSumScoresMembers();
        AlianceRanking::truncate();

        foreach ($sumScoreMembers as $scores) {
            $aliance = Aliance::find($scores->id);
            $aliance->score = $scores->score;
            $aliance->buildScore = $scores->buildScore;
            $aliance->defenseScore = $scores->defenseScore;
            // $aliance->militaryScore = $scores->militaryScore;
            // $aliance->researchScore = $scores->researchScore;
            $aliance->attackScore = $scores->attackScore;
            $this->atualizaRanking($aliance);
            $aliance->save();
        }

        return $sumScoreMembers;
    }
    private function atualizaRanking($dados)
    {
        $findAlianceDelete = AlianceRanking::where('aliance', $dados->aliance);
        if ($findAlianceDelete) {
            $findAlianceDelete->delete();
        }
        $alianceRanking = new AlianceRanking();
        $alianceRanking->aliance = $dados->id;
        $alianceRanking->energy = 100;
        $alianceRanking->score = $dados->score;
        $alianceRanking->buildScore = $dados->buildScore;
        $alianceRanking->labScore = 0;
        $alianceRanking->tradeScore = 0;
        $alianceRanking->attackScore = $dados->attackScore;
        $alianceRanking->defenseScore = $dados->defenseScore;
        $alianceRanking->warScore = 0;
        $alianceRanking->save();
    }
    private function calculaScores($members)
    {

        $aliance = Aliance::find($members[0]->aliance);

        if (!$aliance) {
            return response()->json(['message' => 'Aliança não encontrada.'], 404);
        }

        $aliance->name = $members[0]->name;
        $aliance->founder = $members[0]->founder;
        $aliance->score = 0;
        $aliance->buildScore = 0;
        $aliance->attackScore = 0;
        $aliance->defenseScore = 0;

        foreach ($members as $member) {
            $aliance->score += $member->score;
            $aliance->buildScore += $member->buildScore;
            $aliance->attackScore += $member->attackScore;
            $aliance->defenseScore += $member->defenseScore;
        }

        $aliance->save();
        $findAlianceDelete = AlianceRanking::where('aliance', $aliance->id)->first();

        if ($findAlianceDelete) {
            $findAlianceDelete->delete();
        }
    }
}
