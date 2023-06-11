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

    public function getDadosTradingByResourceAndMarket($resource, $region, $type, $orderby, $column)
    {
        $orderByDirection = $orderby == 'A' ? 'asc' : 'desc';
        $columnOrder = $column ? 't.createdAt' : 't.' . $column;
        $trading = DB::table($this->table . ' as t')->select("t.*", "p.name")
            ->join('market as m', 'm.id', '=', 't.idMarket')
            ->join('planets as planeta', 'planeta.id', '=', 't.idPlanetCreator')
            ->join('players as p', 'p.id', '=', 'planeta.id')
            ->where('m.status', true)
            ->where('t.status', true)
            ->where('m.region', '=', $region)
            ->where('t.resource', '=', $resource)
            ->where('t.type', '=', $type)
            ->orderBy($columnOrder, $orderByDirection)
            ->get();
        return $trading;
    }
    public function getMyResources($player)
    {
        $resources = DB::table('planets as p')
            ->select('p.resource', 'p.region', 'p.uranium', 'p.crystal', 'p.metal', 'p.transportShips')
            ->where('p.player', $player)->first();
            return $resources;
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
