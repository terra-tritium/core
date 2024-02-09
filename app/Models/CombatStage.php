<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CombatStage extends Model
{
    use HasFactory;
    protected $table = 'stages';
    public $timestamps = false;
}