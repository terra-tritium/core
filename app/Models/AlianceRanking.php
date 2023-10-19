<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlianceRanking extends Model
{
    use HasFactory;
    protected $table = 'aliances_ranking';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'position',
        'aliance',
        'description',
        'energy',
        'score',
        'buildScore',
        'labScore',
        'tradeScore',
        'attackScore',
        'defenseScore',
        'warScore',
        'countMembers'
    ];
}
