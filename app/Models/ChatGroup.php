<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ChatGroup extends Model
{
    use HasFactory;
    protected $table = 'chat_group';
    public $timestamps = false;

    protected $fillable = [
        'idAliance',
        'groupName',
        'createdAt',
        'status'
    ];
   
}
