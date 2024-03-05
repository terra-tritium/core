<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Research",
 *     required={"id", "name", "description"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Research 1"),
 *     @OA\Property(property="description", type="string", example="Description of Research 1")
 * )
 */
class Research extends Model
{
    use HasFactory;
    protected $table = 'researchs';
    public $timestamps = false;


}
