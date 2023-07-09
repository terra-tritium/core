<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RotinaDBG extends Model
{
    use HasFactory;
    protected $table = 'rotina';
    public $timestamps = false;

    protected $fillable = [
        'executado'
    ];


    
}
