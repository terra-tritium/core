<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Contries")
 */
class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function show(Country $country)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Country $country)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function destroy(Country $country)
    {
        //
    }

    /**
     * @return mixed
     * @OA\Get (
     *      path="/api/country/list",
     *      summary="List of countries",
     *      tags={"Contries"},
     *      description="List of countries",
     * @OA\Response(response="200", description="Sucesso")
     * )
     */
    public function list() {
        return Country::orderBy('name')->get();
    }
}
