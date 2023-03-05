<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefenseMode extends Model
{
    use HasFactory;
    protected $table = 'defensemodes';
    public $timestamps = false;
}
