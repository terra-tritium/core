<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AlianceRequest extends Model
{
    use HasFactory;
    protected $table = 'aliances_requests';
    public $timestamps = false;

    protected $fillable = [
        'player_id',
        'sentBy',
        'message',
        'status',
        'created_at',
        'updated_at',
        'alianceId'
    ];

    
}
