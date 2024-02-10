<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logbook extends Model
{
    use HasFactory;
    protected $casts = [
        'read' => 'boolean'
    ];

    protected $table = 'logbook';
    public $timestamps = false;
}
