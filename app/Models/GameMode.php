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
}
