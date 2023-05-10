<?php

namespace App\Services;

use App\Models\Aliance;
use App\Models\Player;
use App\Models\Ranking;

class RankingService
{
  public function addPoints (Player $player, $points) {
    $player->score += $points;
    return $player;
  }

    public function getRankings($name = null, $orderBy = null)
    {
        $query = Ranking::query();

        if ($name) {
            $query->where('name', 'LIKE', '%' . $name . '%');
        }

        if ($orderBy) {
            switch ($orderBy) {
                case 'score':
                    $query->orderBy('score', 'desc');
                    break;
                case 'researchScore':
                    $query->orderBy('researchScore', 'desc');
                    break;
                case 'attackScore':
                    $query->orderBy('attackScore', 'desc');
                    break;
                case 'defenseScore':
                    $query->orderBy('defenseScore', 'desc');
                    break;
                case 'buildScore':
                    $query->orderBy('buildScore', 'desc');
                    break;
                default:
                    // Ordenação padrão (pode ser modificada conforme necessidade)
                    $query->orderBy('score', 'desc');
                    break;
            }
        }

        return $query->get();
    }

    public function getAlianceRankings($orderBy = null, $filter = null)
    {
        $query = Aliance::query();

        if ($orderBy) {
            switch ($orderBy) {
                case 'score':
                    $query->orderBy('score', 'desc');
                    break;
                case 'buildScore':
                    $query->orderBy('buildScore', 'desc');
                    break;
                case 'labScore':
                    $query->orderBy('labScore', 'desc');
                    break;
                case 'tradeScore':
                    $query->orderBy('tradeScore', 'desc');
                    break;
                case 'attackScore':
                    $query->orderBy('attackScore', 'desc');
                    break;
                case 'defenseScore':
                    $query->orderBy('defenseScore', 'desc');
                    break;
                case 'warScore':
                    $query->orderBy('warScore', 'desc');
                    break;
                default:
                    // Ordenação padrão (pode ser modificada conforme sua necessidade)
                    $query->orderBy('score', 'desc');
                    break;
            }
        }

        if ($filter) {
            $query->where('name', 'like', "%{$filter}%");
        }

        return $query->get();
    }
}
