<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Safe extends Model
{
    use HasFactory;
    protected $table = 'safe';
    public $timestamps = false;

    protected $fillable = [
        'idPlanetCreator',
        'idPlanetSale',
        'idPlanetPurch',
        'idMarket',
        'idTrading',
        'quantity',
        'price',
        'total',
        'distance',
        'deliveryTime',
        'type',
        'resource',
        'currency',
        'status',
        'createdAt',
        'updatedAt',
        'transportShips',
        'loaded'
    ];


    public function getDadosSafe()
    {
        $results = DB::table($this->table . ' as s')
            ->select('s.*','s.id as safeId','t.status as status_trading','t.idPlanetInterested','t.createdAt')
            ->selectRaw('TIMESTAMPADD(SECOND, s.deliveryTime, s.createdAt) AS tempoFinal')
            ->selectRaw("IF(TIMESTAMPADD(SECOND, s.deliveryTime, s.createdAt) >= NOW(), false, true) AS concluido")
            ->selectRaw('IF(TIMESTAMPDIFF(SECOND, NOW(), TIMESTAMPADD(SECOND, s.deliveryTime, s.createdAt)) > 0, TIMESTAMPDIFF(SECOND, NOW(), TIMESTAMPADD(SECOND, s.deliveryTime, s.createdAt)), 0) AS segundosRestantes')
            // ->selectRaw("IF(TIMESTAMPADD(SECOND, s.deliveryTime, s.createdAt) >= NOW(), TIMESTAMPDIFF(MINUTE, NOW(), TIMESTAMPADD(SECOND, s.deliveryTime, s.createdAt)), 0) AS minutosRestantes")
            ->selectRaw("IF(TIMESTAMPDIFF(SECOND, s.createdAt, NOW()) >= (s.deliveryTime / 2), true, false) AS atingiuMetadeTempo")
            ->selectRaw('IF(s.deliveryTime >= 2, (s.deliveryTime / 2) - TIMESTAMPDIFF(SECOND, s.createdAt, NOW()), 0) AS segundosAtingirMetadeTempo')
            ->selectRaw('TIMESTAMPDIFF(SECOND, s.createdAt, NOW()) AS segundosPassados')
            ->join('trading as t', 't.id', '=', 's.idTrading')
            ->get();
            return $results;
    }
}
