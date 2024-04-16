<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TradingFinished extends Model
{
    use HasFactory;
    protected $table = 'trading_finished';
    public $timestamps = false;
    //subir
    protected $fillable = [
        'idPlanetCreator',
        'idPlanetInterested',
        'quantity',
        'price',
        'distance',
        'deliveryTime',
        'idTrading',
        'status',
        'finishedAt',
        'createdAt',
        'currency',
        'type',
        'idMarket',
        'resource',
        'transportShips'
    ];


    public function getLastTrading()
    {
        $resources = ['crystal', 'metal', 'uranium'];

        $results = DB::table('trading as t')
            ->whereIn('t.resource', $resources)
            ->select('t.id', 't.quantity', 't.price', 't.resource', 't.currency', 't.idMarket')
            ->where('t.status', Trading::STATUS_FINISHED)
            ->whereIn('t.updatedAt', function ($query) {
                $query->select('max_updatedAt')
                    ->from(function ($subquery) {
                        $subquery->selectRaw('MAX(updatedAt) as max_updatedAt')
                            ->from('trading')
                            ->groupBy('resource');
                    }, 'subquery')
                    ->whereColumn('subquery.max_updatedAt', 't.updatedAt');
            })
            ->get();
        return $results;
    }
}
