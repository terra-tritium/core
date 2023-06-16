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
}
