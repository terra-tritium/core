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

        $results = DB::table('trading_finished as tf')
            ->whereIn('tf.resource', $resources)
            ->select('tf.id', 'tf.quantity', 'tf.price', 'tf.resource', 'tf.currency', 'tf.idMarket')
            ->whereIn('tf.finishedAt', function ($query) {
                $query->select('max_finishedAt')
                    ->from(function ($subquery) {
                        $subquery->selectRaw('MAX(finishedAt) as max_finishedAt')
                            ->from('trading_finished')
                            ->groupBy('resource');
                    }, 'subquery')
                    ->whereColumn('subquery.max_finishedAt', 'tf.finishedAt');
            })
            ->get();
        return $results;
    }
}
