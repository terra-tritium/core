<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


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
        $msg = DB::table($this->table . ' as m')
            ->select('us.name as sender', 'ur.name as recipient', 'm.*')
            ->join('users as us', 'us.id', '=', 'm.senderId')
            ->join('users as ur', 'ur.id', '=', 'm.recipientId')
            ->where([['recipientId', '=', $recipientId], ['read', '=', false]])->get();
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
}
