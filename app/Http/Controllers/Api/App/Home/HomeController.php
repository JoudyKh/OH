<?php

namespace App\Http\Controllers\Api\App\Home;

use App\Http\Controllers\Controller;
use App\Services\App\Home\HomeService;
use Illuminate\Http\Request;
use Torann\GeoIP\Facades\GeoIP;
class HomeController extends Controller
{
    public function __construct(protected HomeService $homeService)
    {}
         /**
     * @OA\Get(
     *     path="/home",
     *     tags={"App" , "App - Home"},
     *     summary="get home",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *     @OA\Parameter(
     *         name="finger_print",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Parameter(
     *         name="view",
     *         in="query",
     *         required=true,
     *         @OA\Schema(enum={"home_view"}),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     )
     * )
     */
    public function index(Request $request)
    {
         $visitor_count = visitorsCount($request);
        return success($this->homeService->index(), 200, ['visitor_count' => $visitor_count]);
    }
}
