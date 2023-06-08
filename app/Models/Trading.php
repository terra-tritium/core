<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Trading extends Model
{
    use HasFactory;
    protected $table = 'trading';
    public $timestamps = false;

    protected $fillable = [
        'resource',
        'idPlanetCreator',
        'idMarket',
        'type',
        'price',
        'quantity',
        'total',
        'createdAt',
        'updatedAt',
    ];

    public function getDadosTradingByResourceAndMarket($type, $region){
        $trading = DB::table($this->table . ' as t')->select("t.*","p.name")
                ->join('market as m', 'm.id', '=', 't.idMarket')
                ->join('planets as planeta', 'planeta.id', '=', 't.idPlanetCreator')
                ->join('players as p', 'p.id', '=', 'planeta.id')
                 ->where('m.status', true)
                 ->where('t.status', true)
                ->where('m.region', '=', $region)
                ->where('t.resource','=', $type)
                ->get();
         return $trading;       
    }
    /*
    $msgs = DB::table($this->table . ' as m')
            ->select('us.name as sender', 'ur.name as recipient', 'm.*')
            ->join('users as us', 'us.id', '=', 'm.senderId')
            ->join('users as ur', 'ur.id', '=', 'm.recipientId')
            ->get();
        return $msgs;
    */
    
}
