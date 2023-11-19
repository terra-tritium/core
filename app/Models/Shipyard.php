<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Shipyard extends Model
{
    use HasFactory;
    protected $table = 'shipyard';
    public $timestamps = false;

    public function unitShipyard(){
        return $this->belongsTo(UnitShipyard::class,'unitsShipyard');
    }

    public function getShipyardPlayer($playerId){
        $shipyards = DB::table($this->table .' as t')
            ->select(
                't.player',
                DB::raw('sum(t.quantity) as quantity'),
                'u.name',
                'u.nick',
                'u.description',
                'u.image',
                'u.type'
            )
            ->join('unitsShipyard as u', 'u.id', '=', 't.unit')
            ->where('t.player', $playerId)
            ->groupBy('t.player', 'u.name','u.nick', 'u.description', 'u.image', 'u.type')
            ->get();
        return $shipyards;
    }
}
