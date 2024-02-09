<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Planet;

class Combat extends Model
{
    use HasFactory;
    protected $table = 'combats';
    public $timestamps = false;

    const STATUS_CREATE = 0;
    const STATUS_RUNNING = 1;
    const STATUS_FINISH = 2;
    const STATUS_CANCEL = 3;

    const SIDE_INVASOR = 1;
    const SIDE_LOCAL = 2;

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
