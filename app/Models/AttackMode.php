<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttackMode extends Model
{
    use HasFactory;
    protected $table = 'attackmodes';
    public $timestamps = false;
}
