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
        Schema::table('players', function (Blueprint $table) {
            $table->bigInteger("infesta_time")->nullable();
            $table->bigInteger("simbion_time")->nullable();
            $table->bigInteger("tantra_time")->nullable();
            $table->bigInteger("xantii_time")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn(["infesta_time", "simbion_time", "tantra_time", "xantii_time"]);
        });
    }
};
