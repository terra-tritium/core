<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessJob extends Model
{
    use HasFactory;
    protected $table = 'process_job';

    public $timestamps = false;    

    CONST TYPE_CARRYING = 1;
    CONST TYPE_TRAVEL   = 2;
}
