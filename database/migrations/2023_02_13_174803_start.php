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
        Schema::create('aliances', function (Blueprint $table) {
            $table->id();
            $table->string("description");
            $table->bigInteger("energy");
            $table->bigInteger("score");
            $table->bigInteger("buildScore");
            $table->bigInteger("labScore");
            $table->bigInteger("tradeScore");
            $table->bigInteger("attackScore");
            $table->bigInteger("defenseScore");
            $table->bigInteger("warScore");
        });
        Schema::create('planets', function (Blueprint $table) {
            $table->id();
            $table->string("address");
            $table->string("name");
            $table->string('resource');
            $table->integer("level");
            $table->integer("region");
            $table->integer("quadrant");
            $table->integer("position");
            $table->integer("humanoids");
            $table->string('status');
        });
        Schema::create('countrys', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("code");
            $table->string("image");           
        });
        Schema::create('modes', function (Blueprint $table) {
            $table->id();
            $table->integer("code");
            $table->string("name");
            $table->string("image");
            $table->string("description");
        });
        Schema::create('strategies', function (Blueprint $table) {
            $table->id();
            $table->integer("code");
            $table->string("name");
            $table->string("image");
            $table->string("description");
        });
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("address");
            $table->timestamp("since")->useCurrent();
            $table->foreignId('country')->constrained("countrys");
            $table->integer('gameMode');
            $table->integer('attackStrategy');
            $table->integer('defenderStrategy');
            $table->integer('aliance')->nullable()->unsigned();
            $table->bigInteger("ready")->nullable();
            $table->bigInteger("metal");
            $table->bigInteger("uranium");
            $table->bigInteger("crystal");
            $table->bigInteger("energy");
            $table->bigInteger("battery");
            $table->bigInteger("extraBattery");
            $table->bigInteger("capMetal");
            $table->bigInteger("capUranium");
            $table->bigInteger("capCrystal");
            $table->bigInteger("proMetal");
            $table->bigInteger("proUranium");
            $table->bigInteger("proCrystal");
            $table->integer("pwMetal");
            $table->integer("pwUranium");
            $table->integer("pwCrystal");
            $table->integer("pwEnergy");
            $table->bigInteger("timeMetal")->nullable();
            $table->bigInteger("timeUranium")->nullable();
            $table->bigInteger("timeCrystal")->nullable();
            $table->bigInteger("timeEnergy")->nullable();
            $table->bigInteger("merchantShips");
            $table->bigInteger("score");
            $table->bigInteger("buildScore");
            $table->bigInteger("attackScore");
            $table->bigInteger("defenseScore");
            $table->bigInteger("militaryScore");
        });
        Schema::create('ranking', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("address");
            $table->bigInteger("energy");
            $table->bigInteger("score");
            $table->bigInteger("buildScore");
            $table->bigInteger("attackScore");
            $table->bigInteger("defenseScore");
            $table->bigInteger("militaryScore");
        });
        Schema::create('logbook', function (Blueprint $table) {
            $table->id();
            $table->string("text");
            $table->foreignId('player')->constrained("players");
            $table->foreignId('planet')->constrained("planets");
        });
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string("text");
            $table->string("from");
            $table->string("to");
            $table->boolean("read");
        });
        Schema::create('requires', function (Blueprint $table) {
            $table->id();
            $table->integer('build');
            $table->integer("level");
            $table->bigInteger("metal");
            $table->bigInteger("uranium");
            $table->bigInteger("crystal");
            $table->bigInteger("time");
        });
        Schema::create('builds', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->integer("code");
            $table->string("image");
            $table->text("description");
            $table->integer("maxLevel");
            $table->string("effect");
        });
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('build')->constrained("builds");
            $table->foreignId('planet')->constrained("planets");
            $table->integer("level");
            $table->integer("slot");
            $table->integer("workers");
            $table->bigInteger("ready")->nullable();
        });
        Schema::create('travels', function (Blueprint $table) {
            $table->id();
            $table->string("address");
            $table->string("receptor");
            $table->string("from");
            $table->string("to");
            $table->bigInteger("arrival");
            $table->bigInteger("start");
            $table->integer('action');
            $table->integer('status');
            $table->json("troop");
            $table->json("fleet");
            $table->integer("metal");
            $table->integer("crystal");
            $table->integer("uranium");
            $table->integer("merchantShips");
        });
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("nick");
            $table->text("description");
            $table->string("image");
            $table->string("type");
            $table->integer("defense");
            $table->integer("attack");
            $table->integer("life");
            $table->bigInteger("metal");
            $table->bigInteger("uranium");
            $table->bigInteger("crystal");
            $table->bigInteger("time");
        });
        Schema::create('troop', function (Blueprint $table) {
            $table->id();
            $table->string("address");
            $table->foreignId('planet')->constrained("planets");
            $table->foreignId('unit')->constrained("units");
            $table->bigInteger("quantity");
        });
        Schema::create('fleet', function (Blueprint $table) {
            $table->id();
            $table->string("address");
            $table->foreignId('planet')->constrained("planets");
            $table->foreignId('unit')->constrained("units");
            $table->bigInteger("quantity");
        });
        Schema::create('production', function (Blueprint $table) {
            $table->id();
            $table->string("address");
            $table->foreignId('planet')->constrained("planets");
            $table->json('objects');
            $table->bigInteger("ready");
            $table->boolean("executed");
        });
        Schema::create('researchs', function (Blueprint $table) {
            $table->id();
            $table->integer("code");
            $table->string("title");
            $table->text("description");
            $table->integer("cost");
            $table->string("dependence");
            $table->integer("category");
        });
        Schema::create('researcheds', function (Blueprint $table) {
            $table->id();
            $table->string("address");
            $table->integer("code");
            $table->integer("points");
            $table->integer("cost");
            $table->integer("power");
            $table->bigInteger("timer");
            $table->integer("status");
        });
        Schema::create('effects', function (Blueprint $table) {
            $table->id();
            $table->string("address");
            $table->integer("speedProduceUnit");
            $table->integer("speedProduceShip");
            $table->integer("speedBuild");
            $table->integer("speedResearch");
            $table->integer("speedTravel");
            $table->integer("speedMining");
            $table->integer("costBuild");
            $table->integer("protect");
            $table->integer("extraAttack");
        });
        Schema::create('battles', function (Blueprint $table) {
            $table->id();
            $table->string("attacker");
            $table->string("defender");
            $table->string("attackerDemage")->nullable();
            $table->string("defenderDemage")->nullable();
            $table->integer("attackerStrategy");
            $table->integer("defenderStrategy");
            $table->integer("result")->nullable();
            $table->bigInteger("start");
            $table->integer("stage");
            $table->json('resources')->nullable();;
            $table->json('attackerUnits');
            $table->json('defenderUnits');
        });
        Schema::create('stages', function (Blueprint $table) {
            $table->id();
            $table->integer("number");
            $table->foreignId('battle')->constrained("battles");
            $table->string("attackerDemage");
            $table->string("defenderDemage");
            $table->integer("attackerStrategy");
            $table->integer("defenderStrategy");
            $table->json('attackerUnits');
            $table->json('defenderUnits');
            $table->json('attakerKills');
            $table->json('defenderKills');
            $table->boolean('attackerGaveUp');
            $table->boolean('defenderGaveUp');
        });
        Schema::create('qnames', function (Blueprint $table) {
            $table->id();
            $table->string("quadrant");
            $table->string("name");
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
