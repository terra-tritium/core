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
        'logo'
    ];
    public function getAliances()
    {
        $aliances = DB::table('aliances as a')
            ->select('a.*', DB::raw('(SELECT COUNT(1) FROM aliances_members am WHERE am.idAliance = a.id AND am.status = "A") AS countMembers'))
            ->get();
        return $aliances;
    }

    /**
     * somatoria do level de todas as construções de aliança do jogador
     */
    public function getLevelBuildAliance($playerId)
    {
        $level = DB::table('planets as p')
            ->join('buildings as b', 'p.id', '=', 'b.planet')
            ->where('p.player', $playerId)
            ->where('b.build', 14)
            ->sum('b.level');
        return $level;
    }
}
