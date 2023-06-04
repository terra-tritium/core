<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Resource extends Model
{
    use HasFactory;
    protected $table = 'resource';
    public $timestamps = false;

    protected $fillable = [
        'nameResource',
        'status',
        'createdAt',
        'updatedAt',
    ];

    
}
