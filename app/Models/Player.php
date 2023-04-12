<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Player extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'players';

    public static function getPlayerLogged(){
        return Player::where('user', auth()->user()->id)->first();
    }

    public static function getMyPlanets()
    {
        $planets = DB::table('planets')
                                    ->join('players','players.id','=','planets.player')
                                    ->where('players.user',auth()->user()->id)
                                    ->get();

        return $planets;
    }
}
