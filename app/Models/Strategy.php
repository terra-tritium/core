<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Strategy extends Model
{
    use HasFactory;
    protected $table = 'strategies';
    public $timestamps = false;
    
    const LINE = 1;
    const SNIPER = 2;
    const GUERRILHA = 3;
    const DIAMOND = 4;
    const WEDGE = 5;
    const STAR = 6;
    const DELTA = 7;
    const DIAGONAL = 8;
    const COLUMN = 9;
    const DUAL_COLUMN = 10;
    const FLANKS = 11;

    public function getStrategies($planet)
    {


    }
}
