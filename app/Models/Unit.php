<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Unit",
 *     required={"id", "name"},
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="name", type="string", example="Unit 1")
 * )
 */
class Unit extends Model
{
    use HasFactory;
    protected $table = 'units';
}
