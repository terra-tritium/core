<?php

namespace App\Http\Controllers;

use App\Models\Ranking;
use App\Models\Aliance;
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
}
