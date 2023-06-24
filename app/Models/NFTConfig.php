<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NFTConfig extends Model
{
    use HasFactory;
    protected $table = 'nftconfig';
    public $timestamps = false;
}
