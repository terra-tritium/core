<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        # Colonization
        DB::table('builds')
            ->where('code', 1)
            ->update(['coefficient' => 200]);
        # Energy Collector
        DB::table('builds')
            ->where('code', 2)
            ->update(['coefficient' => 30]);
        # Humanoid factory
        DB::table('builds')
            ->where('code', 3)
            ->update(['coefficient' => 70]);
        # Metal
        DB::table('builds')
            ->where('code', 4)
            ->update(['coefficient' => 30]);
        # uranium
        DB::table('builds')
            ->where('code', 5)
            ->update(['coefficient' => 50]);
        # cristal
        DB::table('builds')
            ->where('code', 6)
            ->update(['coefficient' => 50]);
        # laboratory
        DB::table('builds')
            ->where('code', 7)
            ->update(['coefficient' => 40]);
        # wherehouse
        DB::table('builds')
            ->where('code', 8)
            ->update(['coefficient' => 50]);
        # wherehouse
        DB::table('builds')
            ->where('code', 9)
            ->update(['coefficient' => 50]);
        # batery
        DB::table('builds')
            ->where('code', 10)
            ->update(['coefficient' => 50]);
        # batery
        DB::table('builds')
            ->where('code', 11)
            ->update(['coefficient' => 50]);
        # Shield
        DB::table('builds')
          ->where('code', 12)
          ->update(['coefficient' => 150]);
        # Shield
        DB::table('builds')
          ->where('code', 13)
          ->update(['coefficient' => 60]);
        # Concil
        DB::table('builds')
          ->where('code', 14)
          ->update(['coefficient' => 50]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
