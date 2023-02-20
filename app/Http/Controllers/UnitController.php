<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{

    public function list() {
        return Unit::orderBy('name')->get();
    }
}
