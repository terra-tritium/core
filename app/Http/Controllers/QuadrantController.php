<?php

namespace App\Http\Controllers;

use App\Models\Quadrant;
use Illuminate\Http\Request;

class QuadrantController extends Controller
{

    public function show($code) {
        return Quadrant::where("quadrant", $code)->firstOrFail();
    }
}
