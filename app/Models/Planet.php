<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Planet extends Model
{
    use HasFactory;
    protected $table = 'planets';
    public $timestamps = false;

    /**
     * Return all builds
     *
     * @return HasToMany
     */
    public function buildings(): HasMany
    {
        return $this->hasMany(
            Building::class,
            'planet',
            'id'
        );
    }
}
