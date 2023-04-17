<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Message extends Model
{
    use HasFactory;
    protected $table = 'messages';
    protected $fillable = [
        'senderId',
        'recipientId',
        'content',
        'createdAt',
        'updateAt',
        'status',
        'read'
    ];

    public function getAll(){
        $msgs = DB::table($this->table)->get();
        return $msgs;
    }

    public function getAllByUserSender($senderId){
        $msg = DB::table($this->table)->where('senderId',$senderId)->get();
        return $msg;
    }
    
    public function getMsg(){
        $msg = DB::table($this->table)->where('recipientId','3')->get();
        return $msg;
    }
}
