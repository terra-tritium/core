<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('effects', function (Blueprint $table) {
            $table->integer('player')->constrained("players");
        });
        Schema::table('fleet', function (Blueprint $table) {
            $table->integer('player')->constrained("players");
        });
        Schema::table('planets', function (Blueprint $table) {
            $table->integer('player')->constrained("players");
        });
        Schema::table('production', function (Blueprint $table) {
            $table->integer('player')->constrained("players");
        });
        Schema::table('ranking', function (Blueprint $table) {
            $table->integer('player')->constrained("players");
        });
        Schema::table('researcheds', function (Blueprint $table) {
            $table->integer('player')->constrained("players");
        });
        Schema::table('travels', function (Blueprint $table) {
            $table->integer('player')->constrained("players");
        });
        Schema::table('troop', function (Blueprint $table) {
            $table->integer('player')->constrained("players");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
