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
}
