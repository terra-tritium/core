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
    //new trade
    protected $fillable = [
        'resource',
        'idPlanetCreator',
        'idPlanetInterested',
        'currency',
        'idMarket',
        'type',
        'price',
        'quantity',
        'total',
        'createdAt',
        'updatedAt',
        'distance'
    ];

    public function getDadosTradingByResourceAndMarket($idPlanetaLogado, $resource, $region, $type)
    {
      
        $trading = DB::table($this->table . ' as t')
                            ->select(
                                "t.*",
                                "p.name"
                            )
                            ->join('market as m', 'm.id', '=', 't.idMarket')
                            ->join('planets as planeta', 'planeta.id', '=', 't.idPlanetCreator')
                            ->join('players as p', 'p.id', '=', 'planeta.player')
                            ->where('m.status', true)
                            ->where('t.status', env("TRITIUM_MARKET_STATUS_OPEN"))
                            ->where('m.region', '=', $region)
                            ->where('t.resource', '=', $resource)
                            ->where('t.type', '=', $type)
                            ->orderBy('distance')
                            // ->orderBy($columnOrder, $orderByDirection)
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
    public function getResourceAvailable($player)
    {
        $resources = DB::table('planets as p')
            ->leftJoin('trading as t', function ($join) {
                $join->on('p.player', '=', 't.idPlanetCreator')
                    ->where('t.status', env("TRITIUM_MARKET_STATUS_OPEN"))
                    ->where('t.type', 'S');
            })
            ->where('p.player', $player)
            ->groupBy('p.player', 'p.uranium', 'p.crystal', 'p.metal', 'p.region', 'p.transportShips')
            ->selectRaw('p.player, 
            p.uranium - COALESCE(SUM(CASE WHEN t.resource = "Uranium" THEN t.quantity ELSE 0 END), 0) AS uranium,
            p.crystal - COALESCE(SUM(CASE WHEN t.resource = "Crystal" THEN t.quantity ELSE 0 END), 0) AS crystal,
            p.metal - COALESCE(SUM(CASE WHEN t.resource = "Metal" THEN t.quantity ELSE 0 END), 0) AS metal,
            p.region,
            p.transportShips')
            ->first();
        return $resources;
    }
    public function getAllOrderPlayer($player, $resource)
    {
        $status = [env("TRITIUM_MARKET_STATUS_OPEN"), env("TRITIUM_MARKET_STATUS_PENDING")];
        $orders = DB::table($this->table . ' as t')
            ->select(
                't.id',
                't.idPlanetInterested',
                't.status as statusTrading',
                't.quantity',
                't.type',
                't.resource',
                't.price',
                't.total',
                't.createdAt',
                't.updatedAt',
                'tf.deliveryTime',
                'tf.status as statusFinished',
                'tf.finishedAt'
            )
            ->leftJoin('trading_finished as tf', 'tf.idTrading', '=', 't.id')
            ->where(function ($query) use ($player) {
                $query->where('t.idPlanetCreator', $player)
                    ->orWhere('t.idPlanetInterested', $player);
            })
            ->where('t.resource', $resource)
            ->whereIn('t.status', $status)
            ->orderBy('t.createdAt', 'DESC')
            ->get();
        return $orders;
    }
}
