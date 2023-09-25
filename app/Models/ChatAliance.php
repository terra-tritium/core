<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Chat entre duas alianças
 */
class ChatAliance extends Model
{
    use HasFactory;
    protected $table = 'chat_aliance';
    public $timestamps = false;

    protected $fillable = [
        'idOrigem',
        'idDestino',
        'createdAt',
        'status',
        'message',
        'player'
    ];

    public function getMessageAliance($destino)
    {
        $messages = DB::table('chat_aliance as ca')
            ->select(
                DB::raw('CASE WHEN idDestino = ' . $destino . ' THEN true ELSE false END AS sender'),
                'ca.idOrigem',
                'ca.idDestino',
                'ca.createdAt',
                'ca.status',
                'ca.message',
                'ca.player'
            )
            ->where(function ($query) use ($destino) {
                $query->where('ca.idDestino', $destino)
                    ->orWhere('ca.idOrigem', $destino);
            })

            ->orderBy('ca.createdAt', 'asc')
            ->get();
        return $messages;
    }
}
