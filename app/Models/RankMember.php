<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RankMember extends Model
{
    use HasFactory;
    protected $table = 'rank_member';
    public $timestamps = false;

    protected $fillable = [
        'level',
        'rankName',
        'limit',
        'description',
        'visible'
    ];
    
}
