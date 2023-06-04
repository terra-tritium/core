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
        'idResource',
        'idPlanetCreator',
        'idMarket',
        'type',
        'price',
        'quantity',
        'createdAt',
        'updatedAt',
    ];

    
}
