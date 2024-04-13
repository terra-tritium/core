<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Primeiro, obtenha os IDs necessários
        $minId = DB::table('countrys')->where('name', 'Switzerland')->min('id');
        $maxId = DB::table('countrys')->where('name', 'Switzerland')->max('id');

        // Somente proceda se ambos os IDs existirem e forem diferentes
        if ($minId && $maxId && $minId !== $maxId) {
            // Atualize os players para apontar para o ID correto
            DB::table('players')
                ->where('country', $maxId)
                ->update(['country' => $minId]);

            // Remova a entrada duplicada de Switzerland
            DB::table('countrys')
                ->where('id', $maxId)
                ->delete();
        }
    }

    public function down(): void
    {
        //
    }
};
