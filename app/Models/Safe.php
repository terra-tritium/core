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
        'transportShips'
    ];



    
}
