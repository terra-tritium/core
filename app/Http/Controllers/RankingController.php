<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Aliance;
use Illuminate\Http\Request;

class RankingController extends Controller
{

    private $itensPerPage = 100;

    public function players($address, $type) {

        switch ($type) {
            case "top":
                return Player::orderBy('score', 'DESC')->take(10)->get();
            case "general": 
                return Player::orderBy('score', 'DESC')->paginate($this->itensPerPage);
            case "builder": 
                return Player::orderBy('buildScore', 'DESC')->paginate($this->itensPerPage);
            case "military": 
                return Player::orderBy('militaryScore', 'DESC')->paginate($this->itensPerPage);
            case "attack":
                return Player::orderBy('attackScore', 'DESC')->paginate($this->itensPerPage);
            case "defense":
                return Player::orderBy('defenseScore', 'DESC')->paginate($this->itensPerPage);
            case "energy":
                return Player::orderBy('energy', 'DESC')->paginate($this->itensPerPage);
        }
    }

    public function aliances($address, $type) {
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
