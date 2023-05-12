<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


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
    public function getAllMessegeNotRead($recipientId)
    {
        $msgs = DB::table('users as u')
        ->select('u.id','u.name', DB::raw('MAX(m.createdAt) as createdAt'), DB::raw('MAX(m.read) as `read`'))
        ->join('messages as m', 'u.id', '=', 'm.senderId')
        ->where('m.recipientId', $recipientId)
        ->where('m.read', false)
        ->groupBy('u.id','m.senderId','u.name')
        ->orderBy('read', 'ASC')
        ->orderBy('createdAt', 'DESC')
        ->get();
        return $msgs;
    }
    /**
     * Recupera a ultima msg não lida enviada por um usuario
     */
    public function getLastMessageNotReadBySender($recipientId, $senderId){
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
    public function getSenders($recipientId)
    {
        $messages = DB::table('users')
            ->join('messages', 'users.id', '=', 'messages.senderId')
            ->select('users.name', 'users.id as senderId', DB::raw('MAX(messages.createdAt) as createdAt'), DB::raw('MAX(messages.read) as `read`'))
            ->where('messages.recipientId', $recipientId)
            ->groupBy('users.id', 'users.name', 'messages.senderId')
            ->orderBy('messages.read')
            ->orderByDesc('createdAt')
            ->get();

        return $messages;
    }
    public function getConversation($recipientId, $senderId)
    {
        $messages =  DB::table('messages')
            ->select('*')
            ->selectRaw("CASE WHEN senderId = $senderId THEN true WHEN recipientId = $senderId THEN false END AS sender")
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
        $readAt = Carbon::now();

        DB::table('messages')
            ->where([['read', '=', false], ['senderId', '=', $senderId], ['recipientId', '=', $currentUser]])
            ->update([
                'read' => true,
                'readAt' => $readAt
            ]);
    }
}
