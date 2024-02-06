<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ship extends Model
{
    use HasFactory;
    protected $table = 'ships';

    const SHIP_CRAFT = 1;
    const SHIP_BOMBER = 2;
    const SHIP_CRUISER = 3;
    const SHIP_SCOUT = 4;
    const SHIP_STEALTH = 5;
    const SHIP_FLAGSHIP = 6;
}
