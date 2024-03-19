<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AlianceMember extends Model
{
    use HasFactory;
    protected $table = 'aliances_members';
    public $timestamps = false;

    protected $fillable = [
        'player_id',
        'createdAt',
        'role',
        'status',
        'dateAdmission',
        'dateOf',
        'idAliance',
        'idRank'
    ];

    public function getMembers($alianceId)
    {
        $members = DB::table($this->table . ' as am')
            ->select(
                'am.id',
                'am.player_id',
                'am.idAliance',
                'am.role',
                'am.createdAt',
                'am.dateAdmission',
                'p.name',
                'rm.id as idRank',
                'rm.level',
                'rm.rankName'
            )
            ->join('players as p', 'p.id', '=', 'am.player_id')
            ->leftJoin('rank_member as rm', 'rm.id', '=', 'am.idRank')
            ->where('am.status', 'A')
            ->where('am.idAliance', $alianceId)
            ->orderBy('p.name')
            ->get();
        return $members;
    }
    public function getMembersPending($alianceId)
    {
        $members = DB::table($this->table . ' as am')
            ->select('am.id', 'am.player_id', 'am.idAliance', 'am.role', 'am.createdAt', 'am.dateAdmission', 'p.name')
            ->join('players as p', 'p.id', '=', 'am.player_id')
            ->where('am.status', 'P')
            ->where('am.idAliance', $alianceId)
            ->orderBy('am.createdAt')
            ->get();
        return $members;
    }
    public function getAlianceRanks($idAliance)
    {
        $results = DB::table($this->table . ' as am')
            ->join('rank_member as rm', 'rm.id', '=', 'am.idRank')
            ->select(
                DB::raw('count(rm.id) as count'),
                'rm.level',
                'rm.id as idRank',
                DB::raw('coalesce(rm.limit, 0) as `limit`'),
                DB::raw('(CASE WHEN rm.`limit` >= count(rm.id) THEN false ELSE true END) as roleAvailable'),
                'rm.rankName'
            )
            ->where('am.idAliance', $idAliance)
            ->groupBy('rm.id', 'rm.level', 'rm.limit', 'rm.rankName')
            ->get();
        return $results;
    }

    public function searchUser($id, $search, $parameter = 'name')
    {
        $usersQuery = DB::table('users as u')
            ->select('u.name', 'u.email', 'p.id as idPlayer', 'p.aliance', 'am.status', 'a.name as alianceName')
            ->join('players as p', 'p.id', '=', 'u.id')
            ->leftJoin('aliances_members as am', 'am.player_id', '=', 'p.id')
            ->leftJoin('aliances as a', 'a.id', '=', 'p.aliance')
            ->where('p.id', '!=', $id);

        if ($parameter == 'name') {
            $usersQuery->where(DB::raw('LOWER(u.name)'), 'LIKE', '%' . strtolower($search) . '%');
        } elseif ($parameter == 'email') {
            $usersQuery->where(DB::raw('LOWER(u.email)'), strtolower($search));
        }
        $users = $usersQuery->get();
        return $users;
    }
}
