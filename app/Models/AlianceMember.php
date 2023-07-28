<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlianceMember extends Model
{
    use HasFactory;
    protected $table = 'aliances_members';
    public $timestamps = false;

    protected $fillable = [
        'player_id',
        'createdAt',
        'role',
        'status',
        'dateAdmission',
        'dateOf',
        'idAliance'
    ];
}
