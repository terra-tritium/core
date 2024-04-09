<?php

namespace App\Services;
use App\Models\Travel;
use App\Models\Planet;
use App\Services\PlanetService;
use App\Services\LogService;
use App\Jobs\ChallangeJob;


class ChallangeService
{
    public function startMission ($player, $from, $to)
    {
        if ($this->onMission($from)) { return false; }

        $now = time();

        $planetService = new PlanetService();

        $travelTime =  $planetService->calculeDistance($from, $to);

        $travel = new Travel();
        $travel->from = $from;
        $travel->to = $to;
        $travel->action = Travel::MISSION_CHALLANGE;
        $travel->player = $player;
        $travel->start = $now;
        $travel->arrival = $now + $travelTime;
        $travel->receptor = 0;
        $travel->status = Travel::STATUS_ON_GOING;

        $travel->save();

        ChallangeJob::dispatch($travel)->delay(now()->addSeconds($travelTime));
    }

    public function convert($player, $planetId) {
        $planet = Planet::findOrFail($planetId);

        if ($planet->yellowTrit > 0) {
            $player->yellowTrit += $planet->yellowTrit;
            $player->save();

            $planet->yellowTrit = 0;
            $planet->save();

            $logService = new LogService();
            $logService->notify($player, 'You converted '. $planet->yellowTrit .' yellow tritium from '. $planet->name .'!', 'Convert');
        }
    }

    public function endMission ($player, $from, $to)
    {
        $now = time();

        $planetService = new PlanetService();

        $travelTime =  $planetService->calculeDistance($from, $to);

        $travel = new Travel();
        $travel->from = $to;
        $travel->to = $from;
        $travel->action = Travel::RETURN_CHALLANGE;
        $travel->player = $player;
        $travel->start = $now;
        $travel->arrival = $now + $travelTime;
        $travel->receptor = 0;
        $travel->status = Travel::STATUS_ON_GOING;

        $travel->save();

        ChallangeJob::dispatch($travel, true)->delay(now()->addSeconds($travelTime));
    }

    public function cancelMission ($player, $from, $to)
    {
        $this->endMission($player, $from, $to);
    }

    public function onMission ($planetId)
    {
        $travel = Travel::where([['from', '=', $planetId], ['status', '=', Travel::STATUS_ON_GOING], ['status', '=', Travel::STATUS_RETURN]])->first();

        if ($travel) {
            return true;
        } else {
            return false;
        }
    }

    public function executeMission ($travel)
    {
        $planetFrom = Planet::findOrFail($travel->from);
        $planetTo = Planet::findOrFail($travel->to);

        if ($planetFrom->yellowTrit >= $planetTo->yellowTrit) {

            $yTrit = $planetTo->yellowTrit;

            $planetFrom->yellowTrit += $planetTo->yellowTrit;
            $planetTo->yellowTrit = 0;

            $planetFrom->save();
            $planetTo->save();

            $logService = new LogService();
            $logService->notify($travel->player, 'You won the challange in '. $planetTo->name .'! You got '.$yTrit.' yellot tritium.', 'Challange');

        } else {
            $logService = new LogService();
            $logService->notify($travel->player, 'You lost the challange in '. $planetTo->name .'!', 'Challange');
        }

        $travel->status = Travel::STATUS_FINISHED;

        $travel->save();

        $this->endMission($travel->player, $travel->from, $travel->to);
    }
}
