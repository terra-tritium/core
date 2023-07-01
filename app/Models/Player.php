<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 *
 *  @OA\Schema(
 *     schema="Player",
 *     required={"id", "name"},
 *     @OA\Property(property="id", type="integer", format="int64"),
 *     @OA\Property(property="name", type="string"),
 *
 * )
 */
class Player extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'players';

    public static function getPlayerLogged() {
        if (!Auth::check()) { return false; }

        return DB::table('players')->where('user', auth()->user()->id)->first();
    }

    public static function getMyPlanets()
    {
        if (!Auth::check()) { return false; }

        $player = DB::table('players')->where('user', auth()->user()->id)->first();
        $planets = DB::table('planets')->where('player', $player->id)->get();

        return $planets;
    }
}
