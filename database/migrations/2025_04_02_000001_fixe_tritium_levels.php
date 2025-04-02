<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('buildings')
            ->where('level', '>', 1)
            ->where('build', '=', 1)
            ->update(['level' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

