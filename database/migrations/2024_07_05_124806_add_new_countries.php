<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('countrys')->insert([
            'name' => "Australia",
            'code' => "AU",
            'image' => "au.svg",
        ]);
        
        DB::table('countrys')->insert([
            'name' => "New Zealand",
            'code' => "NZ",
            'image' => "nz.svg",
        ]);
        
        DB::table('countrys')->insert([
            'name' => "Norway",
            'code' => "NO",
            'image' => "no.svg",
        ]);
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
