<?php

namespace App\Services;

use App\Models\AlianceRanking;
use App\Models\Player;
use App\Models\Ranking;
use App\ValueObjects\RankingCategory;

class RankingService
{
    public function addPoints ($points) {
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
}
