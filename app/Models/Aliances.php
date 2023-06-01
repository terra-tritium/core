<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aliances extends Model
{
    use HasFactory;
    protected $table = 'aliances';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'avatar'
    ];

}
