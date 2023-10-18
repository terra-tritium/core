<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TerrainType extends Model{
   
    public $timestamps = false;
    
    protected $fillable = ['terrainType', 'energy_multiplier', 'defenseScore'];
    
}
