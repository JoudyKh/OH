<?php

namespace App\Http\Controllers\Api\General\HomeSlider;

use App\Http\Controllers\Controller;
use App\Models\HomeSlider;
use App\Services\General\HomeSlider\HomeSliderService;
use Illuminate\Http\Request;

class HomeSliderController extends Controller
{
    public function __construct(protected HomeSliderService $homeSliderService)
    {}
           /**
     * @OA\Get(
     *     path="/sliders",
     *     tags={"App" , "App - Slider"},
     *     summary="get all sliders",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             example="any"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             example="data base column name"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contact message created successfully",
     *     ),
     * )
     * @OA\Get(
     *     path="/admin/sliders",
     *     tags={"Admin" , "Admin - Slider"},
     *     summary="get all sliders",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *     @OA\Parameter(
     *         name="trash",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             enum={0, 1},
     *             example=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             example="any"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             example="data base column name"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contact message created successfully",
     *     ),
     * )
     */
    public function index(Request $request)
    {
        authorize('view_home_slider', true);

        try {
            return success($this->homeSliderService->index($request));
        }  catch (\Exception $e) {
            return error($e->getMessage(), [$e->getMessage()], $e->getCode());
        }
    }
     /**
     * @OA\Get(
     *     path="/sliders/{id}",
     *     tags={"App" , "App - Slider"},
     *     summary="show one Slider",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     )
     * )
     * @OA\Get(
     *     path="/admin/sliders/{id}",
     *     tags={"Admin" , "Admin - Slider"},
     *     summary="show one Slider",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     )
     * )
     */
    public function show(HomeSlider $slider)
    {
        authorize('view_home_slider', true);

        try {
            return success($this->homeSliderService->show($slider));
        }  catch (\Exception $e) {
            return error($e->getMessage(), [$e->getMessage()], $e->getCode());
        }
    }
     /**
     * @OA\Get(
     *     path="/sliders/index/cities",
     *     tags={"App" , "App - Slider"},
     *     summary="index cities",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     )
     * )
     * @OA\Get(
     *     path="/admin/sliders/index/cities",
     *     tags={"Admin" , "Admin - Slider"},
     *     summary="index cities",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     )
     * )
     */
    public function indexCities()
    {
        return success($this->homeSliderService->indexCities());
    }
}
