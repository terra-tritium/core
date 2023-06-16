<?php

namespace App\Http\Controllers;

use App\Models\Planet;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 *
 *     @OA\Schema(
 *         schema="Planet",
 *         @OA\Property(
 *             property="id",
 *             type="integer",
 *             example=1
 *         ),
 *         @OA\Property(
 *             property="name",
 *             type="string",
 *             example="Earth"
 *         ),
 *         @OA\Property(
 *             property="quadrant",
 *             type="string",
 *             example="Alpha"
 *         ),
 *         @OA\Property(
 *             property="position",
 *             type="string",
 *             example="123"
 *         )
 *     )
 * )
 **/
class PlanetController extends Controller
{

    /**
     *  * @OA\Get(
     *     path="/planet/{quadrant}/{position}",
     *     operationId="findPlanet",
     *     tags={"Planet"},
     *     summary="Find a planet by quadrant and position",
     *     @OA\Parameter(
     *         name="quadrant",
     *         in="path",
     *         description="Quadrant of the planet",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="position",
     *         in="path",
     *         description="Position of the planet",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Planet found",
     *         @OA\JsonContent(ref="#/components/schemas/Planet")

     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No result"
     *     )
     * )
     * @param $quadrant
     * @param $position
     * @return string
     */
    public function find ($quadrant, $position) {
        try {
            if (empty($quadrantParam) || empty($positionParam)) {
                return response()->json(['error' => 'Invalid quadrant or position'], Response::HTTP_BAD_REQUEST);
            }

            $planet = Planet::where('quadrant', $quadrantParam)->where('position', $positionParam)->first();

            if (!$planet) {
                return response()->json(['error' => 'Planet not found'], Response::HTTP_NOT_FOUND);
            }

            return $planet;
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
     * * @OA\Get(
     *     path="/planet/show/{id}",
     *     operationId="showPlanet",
     *     tags={"Planet"},
     *     summary="Show a planet by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the planet",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Planet found",
     *         @OA\JsonContent(ref="#/components/schemas/Planet")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Planet not found"
     *     )
     * )
     *
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        try {
            $planet = Planet::findOrFail($id);

            return response()->json($planet, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Put(
     *     path="/planet/edit/{planet}",
     *     operationId="updatePlanet",
     *     tags={"Planet"},
     *     summary="Update a planet",
     *     @OA\Parameter(
     *         name="planet",
     *         in="path",
     *         description="Planet ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="New Planet Name"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Planet name successfully updated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="You aren't the owner of this planet",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="You aren't the owner of this planet"
     *             )
     *         )
     *     )
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Planet  $planet
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Planet $planet)
    {
        $player = Player::getPlayerLogged();

        if ($planet->player !== $player->id) {
            return response()->json(['message' => "You aren't the owner of this planet"],
                Response::HTTP_FORBIDDEN);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $planet->name = $request->input('name');
        $planet->save();

        return response()->json(['message' => 'Planet name successfully updated'], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Planet  $planet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Planet $planet)
    {
        //
    }


    /**
     *  @OA\Get(
     *     path="/planet/list",
     *     operationId="listPlanets",
     *     tags={"Planet"},
     *     summary="List all planets for the logged-in player",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Planet")
     *         )
     *     )
     * )
     *
     * @return mixed
     */
    public function list() {
        $player = Player::getPlayerLogged();
        if (!$player) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $planets = Planet::where('player', $player->id)->get();

        return response()->json($planets, Response::HTTP_OK);
    }
}
