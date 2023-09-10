<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Troop extends Model
{
    use HasFactory;
    protected $table = 'troop';
    public $timestamps = false;

    public function unit(){
        return $this->belongsTo(Unit::class,'unit');
    }

    public function getTroopPlayer($playerId){
        $troops = DB::table($this->table .' as t')
            ->select(
                't.player',
                DB::raw('sum(t.quantity) as quantity'),
                'u.name',
                'u.nick',
                'u.description',
                'u.image',
                'u.type'
            )
            ->join('units as u', 'u.id', '=', 't.unit')
            ->where('t.player', $playerId)
            ->groupBy('t.player', 'u.name','u.nick', 'u.description', 'u.image', 'u.type')
            ->get();
        return $troops;
    }
}
