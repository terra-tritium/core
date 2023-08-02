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
        'idAliance'
    ];

    public function getMembers($alianceId)
    {
        $members = DB::table($this->table . ' as am')
            ->select('am.id', 'am.player_id', 'am.idAliance', 'am.role', 'am.createdAt', 'am.dateAdmission', 'p.name')
            ->join('players as p', 'p.id', '=', 'am.player_id')
            ->where('am.status', 'A')
            ->where('am.idAliance', $alianceId)
            ->orderBy('p.name')
            ->get();
        return $members;    
    }
    public function getMembersPending($alianceId){
        $members = DB::table($this->table . ' as am')
        ->select('am.id', 'am.player_id', 'am.idAliance', 'am.role', 'am.createdAt', 'am.dateAdmission', 'p.name')
        ->join('players as p', 'p.id', '=', 'am.player_id')
        ->where('am.status', 'P')
        ->where('am.idAliance', $alianceId)
        ->orderBy('am.createdAt')
        ->get();
        return $members;
    }
}
