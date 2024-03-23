<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Espionage extends Model
{
    use HasFactory;
    protected $table = 'espionage';
    public $timestamps = false;

    CONST TYPE_SPY_RESEARCH = 1; // Military, economy and science
    CONST TYPE_SPY_RESOURCE = 2; // Metal, crystal and uranium
    CONST TYPE_SPY_TROOP    = 3; // Unit
    CONST TYPE_SPY_FLEET    = 4; // Ship


}
