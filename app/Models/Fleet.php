<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Fleet extends Model
{
    use HasFactory;
    protected $table = 'fleet';
    public $timestamps = false;

    public function getFleetPlayer($playerId)
    {
        $fleets = DB::table('fleet as f')
            ->select(
                'f.player',
                DB::raw('sum(f.quantity) as quantity'),
                'u.name',
                'u.nick',
                'u.description',
                'u.image',
                'u.type'
            )
            ->join('units as u', 'u.id', '=', 'f.unit')
            ->where('f.player', $playerId)
            ->groupBy('f.player', 'u.name','u.nick', 'u.description', 'u.image', 'u.type')
            ->get();
        return $fleets;
    }
}
