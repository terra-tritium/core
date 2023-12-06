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

    public function getStrategies($planet)
    {


    }
}
