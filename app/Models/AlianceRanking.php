<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlianceRanking extends Model
{
    use HasFactory;
    protected $table = 'aliances';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'energy',
        'score',
        'buildScore',
        'labScore',
        'tradeScore',
        'attackScore',
        'defenseScore',
        'warScore',
    ];
}
