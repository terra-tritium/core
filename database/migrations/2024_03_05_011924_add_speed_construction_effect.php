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
        $table = 'effects';
        $column = 'speedConstructionBuild';
            Schema::table($table, function (Blueprint $table) use ($column) {
                $table->integer($column)->default(0);
            });
        
            DB::table('modes')->where('code',4)->update(
                ['description' => 'Research Speed +20% / Construction Speed decreases -20%']
            );    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
