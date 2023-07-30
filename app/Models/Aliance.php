<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Aliance extends Model
{
    use HasFactory;
    protected $table = 'aliances';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'avatar'
    ];
    public function getAliances()
    {
        $aliances = DB::table('aliances as a')
            ->select('a.*', DB::raw('(SELECT COUNT(1) FROM aliances_members am WHERE am.idAliance = a.id AND am.status = "A") AS countMembers'))
            ->get();
        return $aliances;    
    }
}
