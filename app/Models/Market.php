<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Market extends Model
{
    use HasFactory;
    protected $table = 'market';
    public $timestamps = false;

    protected $fillable = [
        'quadrant',
        'status',
        'name',
        'createdAt',
        'updatedAt',
    ];

    
}
