<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *  * @OA\Schema(
 *     schema="GameMode",
 *     title="Game Mode",
 *     type="object",
 * )
 */
class GameMode extends Model
{
    use HasFactory;
    protected $table = 'modes';

    CONST MODE_CONQUER = 1;
    CONST MODE_COLONIZER = 2;
    CONST MODE_SPACE_TITAN = 3;
    CONST MODE_RESEARCHER = 4;
    CONST MODE_ENGINEER = 5;
    CONST MODE_PROTECTOR = 6;
    CONST MODE_BUILDER = 7;
    CONST MODE_NAVIGATOR = 8;
    CONST MODE_MINER = 9;
}
