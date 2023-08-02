<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Logo extends Model
{
    use HasFactory;
    protected $table = 'logo';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'alt',
        'available'
    ];
    
}
