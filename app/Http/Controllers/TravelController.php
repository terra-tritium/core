<?php

namespace App\Http\Controllers;

use App\Models\Travel;
use App\Services\TravelService;
use Illuminate\Http\Request;

class TravelController extends Controller
{
    private $itensPerPage = 10;

    protected $travelService;

    public function __construct(TravelService $travelService)
    {
        $this->travelService = $travelService;
    }

    public function list($address) {
        return Travel::where("address", $address)->orderBy('arrival')->paginate($this->itensPerPage);
    }

    public function current($address) {
        return Travel::where([["address", $address], ["status", 1]])->orderBy('arrival')->get();
    }

    public function start (Request $request, $address) {
        $this->travelService->start($address, $request->colect());
    }

    public function back ($address, $travel) {
        $currentTravel = Travel::where([["address", $address], ["id", $travel]])->get();
        if ($currentTravel) {
            $this->travelService->back($travel);
        }
    }
}
