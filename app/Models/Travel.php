<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Planet;

class Travel extends Model
{
    use HasFactory;
    protected $table = 'travels';
    public $timestamps = false;

    public function from(){
        return $this->belongsTo(Planet::class, 'from');
    }

    public function to(){
        return $this->belongsTo(Planet::class, 'to');
    }
}
