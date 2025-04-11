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
        'logo',
        'status'
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
            ->where('b.build', 15)
            ->sum('b.level');
        return $level;
    }
    public static function getSumScoresMembers()
    {
        $aliances = Aliance::select(
            'aliances.name',
            'aliances.id',
            DB::raw('count(1) as countMembers'),
            DB::raw('SUM(players.attackScore) as attackScore'),
            DB::raw('SUM(players.score) as score'),
            DB::raw('SUM(players.buildScore) as buildScore'),
            DB::raw('SUM(players.defenseScore) as defenseScore'),
            DB::raw('SUM(players.militaryScore) as militaryScore'),
            DB::raw('SUM(players.militaryScore) as warScore'),
            DB::raw('SUM(players.researchScore) as researchScore'),
            DB::raw('SUM(players.researchScore) as labScore')
        )
            ->join('aliances_members', 'aliances_members.idAliance', '=', 'aliances.id')
            ->join('players', 'players.id', '=', 'aliances_members.player_id')
            ->where('aliances_members.status', 'A')
            ->groupBy('aliances.id')
            ->groupBy('aliances.name')
            ->get();

        return $aliances;
    }
    public function getMembersAliance($idAliance)
    {
        $members = DB::table('aliances as a')
            ->select('p.*', 'a.name', 'a.founder')
            // ->select('a.*')
            ->join('aliances_members as am', 'am.idAliance', '=', 'a.id')
            ->join('players as p', 'p.id', '=', 'am.player_id')
            ->where('a.id', $idAliance)
            ->where('am.status', 'A')
            ->get();
        return $members;
    }

    public function listAlianceForChat()
    {
        $aliancas = DB::table('aliances as a')
            ->leftJoin(
                DB::raw('(
                    SELECT idDestino as id, COUNT(*) as interacao_count
                    FROM chat_aliance
                    GROUP BY idDestino
                    UNION ALL
                    SELECT idOrigem as id, COUNT(*) as interacao_count
                    FROM chat_aliance
                    GROUP BY idOrigem
        ) ca'), 'a.id', '=', 'ca.id')
            ->select('a.id', 'a.name', 'a.logo', DB::raw('(MAX(ca.interacao_count) > 0) as interacao'))
            ->groupBy('a.id', 'a.name', 'a.logo')
            ->orderByDesc('interacao')
            ->orderBy('a.name')
            ->get();

        return $aliancas;
    }
}
