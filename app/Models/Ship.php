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

    # HP
    const SHIP_CRAFT_HP    = 10;
    const SHIP_SCOUT_HP    = 15;
    const SHIP_STEALTH_HP  = 25;
    const SHIP_BOMBER_HP   = 20;
    const SHIP_CRUISER_HP  = 35;
    const SHIP_FLAGSHIP_HP = 60;

    # Ataque
    const SHIP_CRAFT_ATTACK    = 6;
    const SHIP_SCOUT_ATTACK    = 15;
    const SHIP_STEALTH_ATTACK  = 8;
    const SHIP_BOMBER_ATTACK   = 22;
    const SHIP_CRUISER_ATTACK  = 14;
    const SHIP_FLAGSHIP_ATTACK = 45;

    # Defesa
    const SHIP_CRAFT_DEFENSE    = 0;
    const SHIP_SCOUT_DEFENSE    = 3;
    const SHIP_STEALTH_DEFENSE  = 6;
    const SHIP_BOMBER_DEFENSE   = 2;
    const SHIP_CRUISER_DEFENSE  = 5;
    const SHIP_FLAGSHIP_DEFENSE = 10;

    public static function getAttack(string $type): int {
    return constant("self::SHIP_" . strtoupper($type) . "_ATTACK");
    }
    
    public static function getDefense(string $type): int {
    return constant("self::SHIP_" . strtoupper($type) . "_DEFENSE");
    }
    
    public static function getHP(string $type): int {
    return constant("self::SHIP_" . strtoupper($type) . "_HP");
    }

    /**
     * 
     * update ships set attack = 6, defense = 0, hp = 10 where id = 1; -- Craft
     * update ships set attack = 22, defense = 2, hp = 20 where id = 2; -- Bomber
     * update ships set attack = 14, defense = 5, hp = 35 where id = 3; -- Cruiser
     * update ships set attack = 15, defense = 3, hp = 15 where id = 4; -- Scout
     * update ships set attack = 8, defense = 6, hp = 25 where id = 5; -- Stealth
     * update ships set attack = 45, defense = 10, hp = 60 where id = 6; -- Flagship
     */
}
