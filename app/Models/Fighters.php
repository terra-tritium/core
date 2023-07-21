<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Battle;
use App\Models\Planet;

class Fighters extends Model
{
    use HasFactory;
    protected $table = 'fighters';
    public $timestamps = false;

    public function battle()
    {
        return $this->belongsTo(Battle::class, 'battle');
    }

    public function planet()
    {
        return $this->belongsTo(Planet::class, 'planet');
    }
}
