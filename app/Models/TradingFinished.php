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

    protected $fillable = [
        'idPlanetSale',
        'idPlanetPurch',
        'quantity',
        'price',
        'distance',
        'deliveryTime',
        'idTrading',
        'status',
        'finishedAt',
    ];

    
}
