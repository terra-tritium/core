<?php

namespace App\Http\Controllers;

use App\Models\Ranking;
use App\Models\AlianceRanking;
use App\Models\Player;
use Illuminate\Http\Request;

class RankingController extends Controller
{

    private $itensPerPage = 100;

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
                return AlianceRanking::orderBy('score', 'DESC')->limit(5);
            case "general":
                return AlianceRanking::orderBy('score', 'DESC')->paginate($this->itensPerPage);
            case "builder":
                return AlianceRanking::orderBy('buildScore', 'DESC')->paginate($this->itensPerPage);
            case "military":
                return AlianceRanking::orderBy('militaryScore', 'DESC')->paginate($this->itensPerPage);
            case "attack":
                return AlianceRanking::orderBy('attackScore', 'DESC')->paginate($this->itensPerPage);
            case "defense":
                return AlianceRanking::orderBy('defenseScore', 'DESC')->paginate($this->itensPerPage);
            case "energy":
                return AlianceRanking::orderBy('energy', 'DESC')->paginate($this->itensPerPage);
        }
    }
}
