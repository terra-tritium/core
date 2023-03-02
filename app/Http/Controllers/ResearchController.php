<?php

namespace App\Http\Controllers;

use App\Models\Research;
use App\Models\Researched;
use App\Services\ResearchService;
use Illuminate\Http\Request;

class ResearchController extends Controller
{

    protected $researchService;

    public function __construct(ResearchService $researchService)
    {
        $this->researchService = $researchService;
    }

    public function list() {
        return Research::orderBy('code')->get();
    }

    public function researched($address) {
        return Researched::where("address", $address)->get();
    }

    public function start($address, $code) {
        return $this->researchService->start($address, $code);
    }

    public function done($address, $code) {
        return $this->researchService->done($address, $code);
    }
}
