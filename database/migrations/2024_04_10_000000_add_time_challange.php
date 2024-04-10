<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('challanges', function (Blueprint $table) {
            $table->timestamp("created");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
