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
            $table->string("name");
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
            $table->integer('player')->constrained("players");
            $table->string("name");
            $table->string('resource');
            $table->integer("level");
            $table->string('status');
            $table->integer('type');
            # Position
            $table->string("region");
            $table->string("quadrant");
            $table->integer("position");
            # Workers
            $table->integer("workers");
            $table->integer("workersWaiting");
            $table->integer("workersOnMetal");
            $table->integer("workersOnUranium");
            $table->integer("workersOnCrystal");
            # Resources
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
            $table->bigInteger("ready")->nullable();
            # Power multiplier of resources
            $table->integer("pwMetal");
            $table->integer("pwUranium");
            $table->integer("pwCrystal");
            $table->integer("pwEnergy");
            $table->integer("pwWorker");
            # Using resources
            $table->integer("useEnergyByFactory");
            # Consumes times start
            $table->integer("timeEnergyByFactory")->nullable();
            # Accumulation time start resources
            $table->bigInteger("timeMetal")->nullable();
            $table->bigInteger("timeUranium")->nullable();
            $table->bigInteger("timeCrystal")->nullable();
            $table->bigInteger("timeEnergy")->nullable();
            $table->bigInteger("timeWorker")->nullable();
            # Spaceships Transport 
            $table->bigInteger("transportShips");
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
            $table->integer('user')->constrained("users");
            $table->string("address")->nullable();
            $table->timestamp("since")->useCurrent();
            $table->foreignId('country')->constrained("countrys");
            $table->integer('gameMode');
            $table->integer('attackStrategy');
            $table->integer('defenseStrategy');
            $table->integer('aliance')->nullable()->unsigned();
            $table->bigInteger("ready")->nullable();
            # Score rankings
            $table->bigInteger("score");
            $table->bigInteger("buildScore");
            $table->bigInteger("attackScore");
            $table->bigInteger("defenseScore");
            $table->bigInteger("militaryScore");
            $table->bigInteger("researchScore");
        });
        Schema::create('ranking', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->integer('player')->constrained("players");
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
            $table->unsignedBigInteger('senderId');
            $table->unsignedBigInteger('recipientId');
            $table->string('content',1000);
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('readAt')->nullable();
            $table->boolean('status');
            $table->boolean("read");
            $table->foreign('senderId')->references('id')->on('users');
            $table->foreign('recipientId')->references('id')->on('users');
        });
        Schema::create('builds', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->integer("code");
            $table->string("image");
            $table->text("description");
            $table->string("effect");
            // initial cost
            $table->integer("metalStart");
            $table->integer("uraniumStart");
            $table->integer("crystalStart");
            // initial level
            $table->integer("metalLevel");
            $table->integer("uraniumLevel");
            $table->integer("crystalLevel");
            // coefficient of growth 0% - 100%
            $table->integer("coefficient");
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
            $table->integer('player')->constrained("players");
            $table->integer("receptor");
            $table->string("from");
            $table->string("to");
            $table->bigInteger("arrival");
            $table->bigInteger("start");
            $table->integer('status');
            $table->json("troop");
            $table->json("fleet");
            $table->integer("metal");
            $table->integer("crystal");
            $table->integer("uranium");
            $table->integer("transportShips");
            // 1 - Attack
            // 2 - Defense
            // 3 - Transport
            // 4 - Colonize
            // 5 - Explore
            $table->integer("action");
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
            $table->integer('player')->constrained("players");
            $table->foreignId('planet')->constrained("planets");
            $table->foreignId('unit')->constrained("units");
            $table->bigInteger("quantity");
            $table->bigInteger("dispatch")->default(0);
        });
        Schema::create('fleet', function (Blueprint $table) {
            $table->id();
            $table->integer('player')->constrained("players");
            $table->foreignId('planet')->constrained("planets");
            $table->foreignId('unit')->constrained("units");
            $table->bigInteger("quantity");
        });
        Schema::create('production', function (Blueprint $table) {
            $table->id();
            $table->integer('player')->constrained("players");
            $table->foreignId('planet')->constrained("planets");
            $table->json('objects');
            $table->bigInteger("ready");
            $table->boolean("executed");
        });
        Schema::create('effects', function (Blueprint $table) {
            $table->id();
            $table->integer('player')->constrained("players");
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
        Schema::create('researchs', function (Blueprint $table) {
            $table->id();
            $table->integer("code");
            $table->string("title");
            $table->text("description");
            $table->integer("cost");
            $table->string("dependence");
            $table->integer("category");
            $table->string("effectDescription");
            $table->json('effects');
        });
        Schema::create('researcheds', function (Blueprint $table) {
            $table->id();
            $table->integer('player')->constrained("players");
            $table->integer("code");
            $table->integer("cost");
            $table->integer("progress");
            $table->integer("power");
            $table->bigInteger("start");
            $table->bigInteger("timer");
            $table->bigInteger("points");
            $table->integer("status");
            $table->bigInteger("finish");
        });
        Schema::create('battles', function (Blueprint $table) {
            $table->id();
            $table->foreignId("attack")->constrained("players");
            $table->foreignId("defense")->constrained("players");
            $table->foreignId('planet')->constrained("planets");
            $table->string("attackDemage")->nullable();
            $table->string("defenseDemage")->nullable();
            $table->integer("attackStrategy");
            $table->integer("defenseStrategy");
            $table->integer("result")->nullable();
            $table->bigInteger("start");
            $table->integer("stage");
            $table->json('resources')->nullable();
            $table->json('attackUnits');
            $table->json('defenseUnits');
            $table->json('attackReserve');
            $table->json('defenseReserve');
            $table->json('attackSlots')->nullable();
            $table->json('defenseSlots')->nullable();
        });
        Schema::create('stages', function (Blueprint $table) {
            $table->id();
            $table->integer("number");
            $table->foreignId('battle')->constrained("battles");
            $table->string("attackDemage");
            $table->string("defenseDemage");
            $table->integer("attackStrategy");
            $table->integer("defenseStrategy");
            $table->json('attackUnits');
            $table->json('defenseUnits');
            $table->json('attackKills');
            $table->json('defenseKills');
            $table->json('attackSlots');
            $table->json('defenseSlots');
            $table->json('attackReserve');
            $table->json('defenseReserve');
            $table->boolean('attackGaveUp');
            $table->boolean('defenseGaveUp');
        });
        Schema::create('qnames', function (Blueprint $table) {
            $table->id();
            $table->string("quadrant");
            $table->string("name");
            $table->string("x");
            $table->string("y");
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
