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
        return DB::table('players')->where('user', auth()->user()->id)->first();
    }

    public static function getMyPlanets()
    {
        $player = DB::table('players')->where('user', auth()->user()->id)->first();
        $planets = DB::table('planets')->where('player', $player->id)->get();

        return $planets;
    }
}
