<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Schema(
 *     schema="Message",
 *     required={"id", "content", "sender", "recipient"},
 *     @OA\Property(property="id", type="integer", format="int64", description="ID da mensagem"),
 *     @OA\Property(property="content", type="string", description="Conteúdo da mensagem"),
 *     @OA\Property(property="sender", type="integer", format="int64", description="ID do remetente"),
 *     @OA\Property(property="recipient", type="integer", format="int64", description="ID do destinatário"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Data e hora de criação da mensagem"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Data e hora de atualização da mensagem")
 * )
 */
class Message extends Model
{
    use HasFactory;
    protected $table = 'messages';
    public $timestamps = false;

    protected $fillable = [
        'senderId',
        'recipientId',
        'content',
        'createdAt',
        'readAt',
        'status',
        'read'
    ];

    public function getAll()
    {
        $msgs = DB::table($this->table . ' as m')
            ->select('us.name as sender', 'ur.name as recipient', 'm.*')
            ->join('users as us', 'us.id', '=', 'm.senderId')
            ->join('users as ur', 'ur.id', '=', 'm.recipientId')
            ->get();
        return $msgs;
    }

    public function getAllByUserSender($senderId)
    {
        $msg = DB::table($this->table . ' as m')
            ->select('m.senderId', 'us.name as sender')
            ->join('users as us', 'us.id', '=', 'm.senderId')
            ->where('senderId', $senderId)
            ->groupBy('m.senderId')
            ->orderBy('m.createdAt')->get();
        return $msg;
    }

    /**
     *
     */
    public function getAllMessageNotRead($recipientId)
    {
        $msgs = DB::table('users as u')
            ->select('u.id', 'u.name', DB::raw('MAX(m.createdAt) as createdAt'), DB::raw('MAX(m.read) as `read`'))
            ->join('messages as m', 'u.id', '=', 'm.senderId')
            ->where('m.recipientId', $recipientId)
            ->where('m.read', false)
            ->groupBy('u.id', 'm.senderId', 'u.name')
            ->orderBy('read', 'ASC')
            ->orderBy('createdAt', 'DESC')
            ->get();
        return $msgs;
    }
    /**
     * Recupera a ultima msg não lida enviada por um usuario
     */
    public function getLastMessageNotReadBySender($recipientId, $senderId)
    {
        $msg = DB::table('messages as m')
            ->where('recipientId', $recipientId)
            ->where('senderId', $senderId)
            ->where('read', false)
            ->orderBy('createdAt', 'desc')
            ->first();
        return $msg;
    }

    public function getAllByUserRecipient($recipientId)
    {
        $msg = DB::table($this->table . ' as m')
            ->select('m.senderId', 'us.name as sender')
            ->join('users as us', 'us.id', '=', 'm.senderId')
            ->where('recipientId', $recipientId)
            ->groupBy(['m.senderId', 'us.name'])
            ->orderBy('us.name')
            ->get();
        return $msg;
    }
    public function getAllMessageSenderForRecipent($senderId, $recipientId)
    {
        $msgs = DB::table($this->table . ' as m')
            ->where([['m.recipientId', '=', $recipientId], ['m.senderId', '=', $senderId]])
            ->orderBy('m.createdAt')->get();
        return $msgs;
    }

    /**
     * Pegar os usuário onde ja tiveram interações
     */
    public function getSenders($userId)
    {
        $users = DB::table('messages as m')
            ->select(DB::raw('CASE WHEN recipientId = ' . $userId . ' THEN senderId ELSE recipientId END AS id_usuario'))
            ->select('users.name', 'users.id as userId')
            ->selectRaw('MAX(m.createdAt) AS createdAt')
            ->selectRaw('SUM(m.read = 0) AS countNotRead')
            ->join('users', function ($join) use ($userId) {
                $join->on('users.id', '=', DB::raw('CASE WHEN recipientId = ' . $userId . ' THEN senderId ELSE recipientId END'));
            })
            ->where(function ($query) use ($userId) {
                $query->where('senderId', $userId)
                    ->orWhere('recipientId', $userId);
            })
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('countNotRead')
            ->orderByDesc('createdAt')
            ->get();

        return $users;
    }
    public function getConversation($recipientId, $senderId)
    {
        $messages =  DB::table('messages')
            ->select('*')
            ->selectRaw(
                "CASE WHEN senderId = ? THEN true WHEN recipientId = ? THEN false END AS sender",
                [$senderId, $senderId]
            )
            ->where(function ($query) use ($senderId, $recipientId) {
                $query->where('senderId', $senderId)
                    ->where('recipientId', $recipientId);
            })
            ->orWhere(function ($query) use ($senderId, $recipientId) {
                $query->where('senderId', $recipientId)
                    ->where('recipientId', $senderId);
            })
            ->orderBy('createdAt')
            ->get();
        return $messages;
    }

    public function readMessagesForUser($senderId, $currentUser)
    {
        DB::table('messages')
            ->where([['read', '=', false], ['senderId', '=', $senderId], ['recipientId', '=', $currentUser]])
            ->update([
                'read' => true,
                'readAt' => time()
            ]);
    }
    public function searchUserByName($id, $search)
    {
        $users = User::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%'])
            ->where('id', '!=', $id)
            ->select('id', 'name', 'email','id as userId')
            ->get();
        return $users;
    }
    public function searchUserByEmail($id, $search)
    {
        $users = User::whereRaw('LOWER(email) LIKE ?', ['%' . strtolower($search) . '%'])
            ->where('id', '!=', $id)
            ->select('id', 'name', 'email', 'id as userId')
            ->get();
        return $users;
    }
}
