<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MessageGroup extends Model
{
    use HasFactory;
    protected $table = 'message_group';
    public $timestamps = false;

    protected $fillable = [
        'idChatGroup',
        'remetenteId',
        'message',
        'createdAt',
        'status'
    ];

    public function getMessagesGroupAliance($idAliance, $idRementente)
    {
        
        $messages = DB::table('chat_group as cg')
            ->join('message_group as mg', 'mg.idChatGroup', '=', 'cg.id')
            ->join('players as p', 'p.id', '=', 'mg.remetenteId')
            ->select(
                'mg.id',
                'cg.idAliance',
                'mg.remetenteId',
                'p.name as nameRemetente',
                'mg.idChatGroup',
                'mg.message',
                'cg.groupName',
                'mg.createdAt',
                'mg.status',
                DB::raw("CASE WHEN mg.remetenteId = $idRementente THEN true ELSE false END AS sender")
            )
            ->where('cg.idAliance', '=', $idAliance)
            ->orderBy('mg.createdAt', 'DESC')
            ->limit(100)
            ->get();
        return $messages;    
    }
}
