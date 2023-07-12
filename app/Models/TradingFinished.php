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

    
}
