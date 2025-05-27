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
        Schema::table('aliances', function (Blueprint $table) {
            $table->bigInteger('jump')->default(0)->nullable();
            $table->bigInteger('firepower')->default(0)->nullable();
            $table->integer("jump_qtd")->default(0)->nullable();
            $table->integer("firepower_qtd")->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aliances', function (Blueprint $table) {
            $table->dropColumn("jump", "firepower", "jump_qtd", "firepower_qtd");
        });
    }
};
