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
            'name' => "Iran",
            'code' => "IR",
            'image' => "ir.svg",
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
