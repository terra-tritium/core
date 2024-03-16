<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       
        $table = 'players';
        $column = 'gameModeUpdated';
            Schema::table($table, function (Blueprint $table) use ($column) {
                $table->integer($column)->nullable();
            });
        
      
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       
    }
};
