<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NFTEffect extends Model
{
    use HasFactory;
    protected $table = 'nft-effects';

    public $timestamps = false;    

    protected $fillable = ['type', 'rarity', 'effect', 'value'];
}
