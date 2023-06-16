<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Researched
 *
 *  @OA\Schema(
 *     schema="Researched",
 *     title="Researched",
 *     description="Researched item",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="player", type="integer", example="1"),
 *     @OA\Property(property="research_item", type="string", example="Research Item 1")
 * )
 * @package App\Models
 */
class Researched extends Model
{
    use HasFactory;
    protected $table = 'researcheds';
    public $timestamps = false;
}
