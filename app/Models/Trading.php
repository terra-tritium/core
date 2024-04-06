<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Trading extends Model
{
    CONST STATUS_CANCELED = 0;
    CONST STATUS_OPEN = 1 ;
    CONST STATUS_FINISHED = 2;
    CONST STATUS_PENDING = 3;

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
                "p.name",
                DB::raw("(SELECT origin.calc_distancia(p.id, $idPlanetaLogado)) AS distance"),
            )
            ->join('market as m', 'm.id', '=', 't.idMarket')
            ->join('planets as planeta', 'planeta.id', '=', 't.idPlanetCreator')
            ->join('players as p', 'p.id', '=', 'planeta.player')
            ->where('m.status', true)
            ->where('t.status',  Trading::STATUS_OPEN)
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
            ->select('p.resource', 'p.region', 'p.uranium', 'p.crystal', 'p.metal', 'pp            $planetOrigim->transportShips += $this->transportShips ;
            .transportShips')
            ->leftJoin('player as pp', 'pp.id', '=', 'p.player')
            ->where('p.player', $player)->first();
        return $resources;
    }
    public function getResourceAvailable($player)
    {
        $resources = DB::table('planets as p')
            ->leftJoin('trading as t', function ($join) {
                $join->on('p.player', '=', 't.idPlanetCreator')
                    ->where('t.status', Trading::STATUS_OPEN)
                    ->where('t.type', 'S');
            })
            ->leftJoin('players as pp', 'pp.id', '=', 'p.player')
            ->where('p.player', $player)
            ->groupBy('p.player', 'p.uranium', 'p.crystal', 'p.metal', 'p.region', 'pp.transportShips')
            ->selectRaw('p.player,
            p.uranium - COALESCE(SUM(CASE WHEN t.resource = "Uranium" THEN t.quantity ELSE 0 END), 0) AS uranium,
            p.crystal - COALESCE(SUM(CASE WHEN t.resource = "Crystal" THEN t.quantity ELSE 0 END), 0) AS crystal,
            p.metal - COALESCE(SUM(CASE WHEN t.resource = "Metal" THEN t.quantity ELSE 0 END), 0) AS metal,
            p.region,
            pp.transportShips')
            ->first();
        return $resources;
    }
    public function getAllOrderByPlanet($planet, $resource)
    {
        $status = [Trading::STATUS_OPEN, Trading::STATUS_PENDING];
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
            ->where(function ($query) use ($planet) {
                $query->where('t.idPlanetCreator', $planet)
                    ->orWhere('t.idPlanetInterested', $planet);
            })
            ->where('t.resource', $resource)
            ->whereIn('t.status', $status)
            ->orderBy('t.createdAt', 'DESC')
            ->get();
        return $orders;
    }
}
