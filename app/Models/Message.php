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
            ->select('us.name as sender', 'ur.name as recipient', 'm.*')
            ->join('users as us', 'us.id', '=', 'm.senderId')
            ->join('users as ur', 'ur.id', '=', 'm.recipientId')
            ->where('senderId', $senderId)->get();
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
            ->select('us.name as sender', 'ur.name as recipient', 'm.*')
            ->join('users as us', 'us.id', '=', 'm.senderId')
            ->join('users as ur', 'ur.id', '=', 'm.recipientId')->where('recipientId', $recipientId)->get();
        return $msg;
    }
    
}
