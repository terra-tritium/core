<?php

namespace App\Services;

use App\Models\Effect;

class BonusService
{
  public function addSpeedProduceUnit ($player, $value) {
    $effect = Effect::where('player', $player->id)->firstOrFail();
    $effect->speedProduceUnit += $value;
    $effect->save();
  }

  public function addSpeedProduceShip ($player, $value) {
    $effect = Effect::where('player', $player->id)->firstOrFail();
    $effect->speedProduceShip += $value;
    $effect->save();
  }

  public function addSpeedBuild ($player, $value) {
    $effect = Effect::where('player', $player->id)->firstOrFail();
    $effect->speedBuild += $value;
    $effect->save();
  }

  public function addSpeedResearch ($player, $value) {
    $effect = Effect::where('player', $player->id)->firstOrFail();
    $effect->speedResearch += $value;
    $effect->save();
  }

  public function addSpeedTravel ($player, $value) {
    $effect = Effect::where('player', $player->id)->firstOrFail();
    $effect->speedTravel += $value;
    $effect->save();
  }

  public function addSpeedMining ($player, $value) {
    $effect = Effect::where('player', $player->id)->firstOrFail();
    $effect->speedMining += $value;
    $effect->save();
  }

  public function addProtect ($player, $value) {
    $effect = Effect::where('player', $player->id)->firstOrFail();
    $effect->protect += $value;
    $effect->save();
  }

  public function addExtraAttack ($player, $value) {
    $effect = Effect::where('player', $player->id)->firstOrFail();
    $effect->extraAttack += $value;
    $effect->save();
  }

  public function addDiscountEnergy ($player, $value) {
    $effect = Effect::where('player', $player->id)->firstOrFail();
    $effect->discountEnergy += $value;
    $effect->save();
  }

  public function addDiscountHumanoid ($player, $value) {
    $effect = Effect::where('player', $player->id)->firstOrFail();
    $effect->discountHumanoid += $value;
    $effect->save();
  }

  public function addDiscountBuild ($player, $value) {
    $effect = Effect::where('player', $player->id)->firstOrFail();
    $effect->discountBuild += $value;
    $effect->save();
  }
}
