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
   
}
