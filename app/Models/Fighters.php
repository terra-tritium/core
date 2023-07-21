<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fighters extends Model
{
    use HasFactory;
    protected $table = 'fighters';
    public $timestamps = false;

    public function battle()
    {
        return $this->belongsTo(Battle::class, 'battle');
    }
}
