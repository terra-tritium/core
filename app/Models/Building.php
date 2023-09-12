<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Building",
 *     required={"id", "name", "description"},
 *     @OA\Property(property="id", type="integer", description="Building ID"),
 *     @OA\Property(property="name", type="string", description="Building name"),
 *     @OA\Property(property="description", type="string", description="Building description")
 * )
 */
class Building extends Model
{
    use HasFactory;
    protected $table = 'buildings';
    public $timestamps = false;

    public function build()
    {
        return $this->hasOne(
            Build::class,
            'id',
            'build'
        );
    }

    public function planet()
    {
        return $this->belongsTo(Planet::class, 'id', 'planet');
    }
}
