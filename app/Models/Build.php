<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *  @OA\Schema(
 *     schema="Build",
 *     required={"id", "name", "code"},
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="name", type="string", example="Build Name"),
 *     @OA\Property(property="code", type="string", example="ABC123")
 * )
 */
class Build extends Model
{
    use HasFactory;
    protected $table = 'builds';

    CONST COLONIZATION = 3;
    CONST ENERGYCOLLECTOR = 4;
    CONST HUMANOIDFACTORY = 5;
    CONST METALMINING = 6;
    CONST URANIUMMINING = 7;
    CONST CRYSTALMINING = 6;
    CONST LABORATORY = 9;
    CONST WAREHOUSE = 10;
    CONST SHIPYARD = 11;
    CONST BATERYHOUSE = 12;
    CONST MILITARYCAMP = 13;
    CONST SHIELD = 14;
    CONST MARKET = 15;
    CONST GALACTICCOUNCIL = 16;

    public function building()
    {
        return $this->belongsTo(Building::class, 'id', 'build');
    }
}
