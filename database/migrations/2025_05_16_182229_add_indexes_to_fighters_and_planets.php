<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('fighters', function (Blueprint $table) {
            $table->index(['player', 'combat'], 'idx_fighters_player_combat');
            $table->index('planet', 'idx_fighters_planet');
        });

        Schema::table('planets', function (Blueprint $table) {
            $table->index('player', 'idx_planets_player');
            $table->index('id', 'idx_planets_id'); // geralmente já tem, mas garantimos
        });
    }

    public function down()
    {
        Schema::table('fighters', function (Blueprint $table) {
            $table->dropIndex('idx_fighters_player_combat');
            $table->dropIndex('idx_fighters_planet');
        });

        Schema::table('planets', function (Blueprint $table) {
            $table->dropIndex('idx_planets_player');
            $table->dropIndex('idx_planets_id');
        });
    }
};
