<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('builds')
            ->where('name', 'Colonization')
            ->update(['coefficient' => 150]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
