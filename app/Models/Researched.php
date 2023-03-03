<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Researched extends Model
{
    use HasFactory;
    protected $table = 'researcheds';
    public $timestamps = false;
}