<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('challanges', function (Blueprint $table) {
            $table->id()->index();
            $table->integer("first");
            $table->integer("second");
            $table->integer("third");
            $table->integer("fourth");
            $table->integer("fifth");
            $table->integer("firstScore");
            $table->integer("secondScore");
            $table->integer("thirdScore");
            $table->integer("fourthScore");
            $table->integer("fifthScore");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
