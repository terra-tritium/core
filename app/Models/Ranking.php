<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ranking extends Model
{
    use HasFactory;
    protected $table = 'ranking';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'player',
        'energy',
        'score',
        'buildScore',
        'attackScore',
        'defenseScore',
        'militaryScore',
        'alianceName',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class, 'player');
    }
}
