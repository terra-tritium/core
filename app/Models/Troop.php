<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Troop extends Model
{
    use HasFactory;
    protected $table = 'troop';
    public $timestamps = false;

    public function unit(){
        return $this->belongsTo(Unit::class,'unit');
    }
}
