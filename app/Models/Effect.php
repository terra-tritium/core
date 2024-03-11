<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Effect extends Model
{
    use HasFactory;
    protected $table = 'effects';
    public $timestamps = false;

    protected $attributes = [
        'speedProduceUnit' => 0,
        'speedProduceShip' => 0,
        'speedBuild' => 0,
        'speedResearch' => 0,
        'speedTravel' => 0,
        'speedMining' => 0,
        'plasmaTechnology' => 0,
        'protect' => 0,
        'extraAttack' => 0,
        'discountEnergy' => 0,
        'discountHumanoid' => 0,
        'discountBuild' => 0,
        'speedConstructionBuild' => 0
    ];

    
    public function zerar()
    {
        $this->setAttribute('speedProduceUnit', 0);
        $this->setAttribute('speedProduceShip', 0);
        $this->setAttribute('speedBuild', 0);
        $this->setAttribute('speedResearch', 0);
        $this->setAttribute('speedTravel', 0);
        $this->setAttribute('speedMining', 0);
        $this->setAttribute('plasmaTechnology', 0);
        $this->setAttribute('protect', 0);
        $this->setAttribute('extraAttack', 0);
        $this->setAttribute('discountEnergy', 0);
        $this->setAttribute('discountHumanoid', 0);
        $this->setAttribute('discountBuild', 0);
        $this->setAttribute('speedConstructionBuild', 0);
    }

  

}
