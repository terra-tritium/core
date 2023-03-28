<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quadrant extends Model
{
    use HasFactory;
    protected $table = 'qnames';
    public $timestamps = false;
}
