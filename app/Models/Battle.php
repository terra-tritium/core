<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Planet;

class Battle extends Model
{
    use HasFactory;
    protected $table = 'battles';
    public $timestamps = false;

    public function planet()
    {
        return $this->belongsTo(Planet::class, 'planet');
    }

    public function addAttackUnits($units)
    {
        $unitsArray = json_decode($this->attackUnits);
        $unitsArray = array_push($unitsArray, $units);
        $this->attackUnits = json_encode($unitsArray);
        $this->save();
    }

    public function addDefenseUnits($units)
    {
        $unitsArray = json_decode($this->defenseUnits);
        $unitsArray = array_push($unitsArray, $units);
        $this->defenseUnits = json_encode($unitsArray);
        $this->save();
    }
}
