<?php

namespace App\Http\Controllers;

use App\Models\Quadrant;
use App\Models\Planet;
use App\Models\Player;
use App\Services\QuadrantService;
use Illuminate\Http\Request;
use stdClass;

class QuadrantController extends Controller
{

    protected $quadrantService;

    public  function __construct(QuadrantService $quadrantService)
    {
        $this->quadrantService =  $quadrantService;
    }

     /**
     * Display the specified resource.
     *
     * @param  \App\Models\Quadrant  $quadrant
     * @return \Illuminate\Http\Response
     *
     * @OA\Get (
     *     path="/api/quadrant/show/{code}/{planet?}",
     *     tags={"Quadrant"},
     *     summary="Get details Quadrant ",
     *     security={
     *         {"bearerAuthTroop": {}}
     *     },
     *     @OA\Response(response="200", description="Resposta bem-sucedida")
     * )
     *  @OA\SecurityScheme(
     *     type="http",
     *     scheme="bearer",
     *     securityScheme="bearerAuthTroop"
     * )
     *
     */
    public function show($code, $planet = 0) {

        $distante = $this->quadrantService->calcDistanceQuadrant($code, $planet);

        $quadrant = Quadrant::where("quadrant", $code)->firstOrFail();
        $quadrant->distance = $distante ;

        return $quadrant ;
    }

    public function planets($quadrant) {
        return Planet::where("quadrant", $quadrant)->get();
    }

    public function map($region) {

        $quadrants = [];

        switch ($region) {
            case 'A':
                $quadrants = Quadrant::whereBetween('id', [1, 100])->get();
                break;
            case 'B':
                $quadrants = Quadrant::whereBetween('id', [101, 200])->get();
                break;
            case 'C':
                $quadrants = Quadrant::whereBetween('id', [201, 300])->get();
                break;
            case 'D':
                $quadrants = Quadrant::whereBetween('id', [301, 400])->get();
                break;
            case 'E':
                $quadrants = Quadrant::whereBetween('id', [401, 500])->get();
                break;
            case 'F':
                $quadrants = Quadrant::whereBetween('id', [501, 600])->get();
                break;
            case 'G':
                $quadrants = Quadrant::whereBetween('id', [601, 700])->get();
                break;
            case 'H':
                $quadrants = Quadrant::whereBetween('id', [701, 800])->get();
                break;
            case 'I':
                $quadrants = Quadrant::whereBetween('id', [801, 900])->get();
                break;
            case 'J':
                $quadrants = Quadrant::whereBetween('id', [901, 1000])->get();
                break;
            case 'K':
                $quadrants = Quadrant::whereBetween('id', [1001, 1100])->get();
                break;
            case 'L':
                $quadrants = Quadrant::whereBetween('id', [1101, 1200])->get();
                break;
            case 'M':
                $quadrants = Quadrant::whereBetween('id', [1201, 1300])->get();
                break;
            case 'N':
                $quadrants = Quadrant::whereBetween('id', [1301, 1400])->get();
                break;
            case 'O':
                $quadrants = Quadrant::whereBetween('id', [1401, 1500])->get();
                break;
            case 'P':
                $quadrants = Quadrant::whereBetween('id', [1501, 1600])->get();
                break;
        }

        return $quadrants;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Quadrant  $quadrant
     * @return \Illuminate\Http\Response
     *
     * @OA\Get (
     *     path="/api/quadrant/distance/{origin}/{destiny}",
     *     tags={"Quadrant"},
     *     summary="Get details Quadrant ",
     *     security={
     *         {"bearerAuthTroop": {}}
     *     },
     *     @OA\Response(response="200", description="Resposta bem-sucedida")
     * )
     *  @OA\SecurityScheme(
     *     type="http",
     *     scheme="bearer",
     *     securityScheme="bearerAuthTroop"
     * )
     *
     */
    public function calcDistancePlants($origin, $destiny) {

        $player = Player::getPlayerLogged();

        $platets = new stdClass();

        $planetOrigin = Planet::find($origin);
        $planetDestiny = Planet::find($destiny);

        $platets->origin = $origin;
        $platets->originName = $planetOrigin->quadrant.' : '.$planetOrigin->name ;
        $platets->destiny  =  $destiny;
        $platets->destinyName  =  $planetDestiny->quadrant.' : '.$planetDestiny->name ;

        $distante = $this->quadrantService->calcDistancePlanets($player, $origin, $destiny);

        $platets->distance = $distante;

        return $platets ;
    }
}
