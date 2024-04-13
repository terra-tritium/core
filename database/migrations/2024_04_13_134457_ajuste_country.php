<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /*Vamos manter como o correto a primeira aparição de Switzerland*/
        DB::statement("
            UPDATE players 
            SET country = (SELECT MIN(id) FROM countrys WHERE name = 'Switzerland')
            WHERE country = (SELECT MAX(id) FROM countrys WHERE name = 'Switzerland')
        ");

        /**Vamos remover a segunda aparição de Switzerland */
        DB::statement("
            DELETE FROM countrys 
            WHERE id = (SELECT MAX(id) FROM countrys WHERE name = 'Switzerland')
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
