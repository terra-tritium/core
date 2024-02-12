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

    const SHIP_CRAFT_HP = 10;
    const SHIP_BOMBER_HP = 20;
    const SHIP_CRUISER_HP = 30;
    const SHIP_SCOUT_HP = 5;
    const SHIP_STEALTH_HP = 15;
    const SHIP_FLAGSHIP_HP = 50;

    const SHIP_CRAFT_ATTACK = 5;
    const SHIP_BOMBER_ATTACK = 10;
    const SHIP_CRUISER_ATTACK = 15;
    const SHIP_SCOUT_ATTACK = 2;
    const SHIP_STEALTH_ATTACK = 7;
    const SHIP_FLAGSHIP_ATTACK = 25;

    const SHIP_CRAFT_DEFENSE = 5;
    const SHIP_BOMBER_DEFENSE = 10;
    const SHIP_CRUISER_DEFENSE = 15;
    const SHIP_SCOUT_DEFENSE = 2;
    const SHIP_STEALTH_DEFENSE = 7;
    const SHIP_FLAGSHIP_DEFENSE = 25;
}
