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

    CONST COLONIZATION = 1;
    CONST ENERGYCOLLECTOR = 2;
    CONST HUMANOIDFACTORY = 3;
    CONST METALMINING = 4;
    CONST URANIUMMINING = 5;
    CONST CRYSTALMINING = 6;
    CONST LABORATORY = 7;
    CONST WAREHOUSE = 8;
    CONST SHIPYARD = 9;
    CONST BATERYHOUSE = 10;
    CONST MILITARYCAMP = 11;
    CONST SHIELD = 12;
    CONST MARKET = 13;
    CONST GALACTICCOUNCIL = 14;

    public function building()
    {
        return $this->belongsTo(Building::class, 'id', 'build');
    }
}
