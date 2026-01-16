<?php

namespace App\Http\Controllers\Api\General\University;

use App\Http\Controllers\Controller;
use App\Services\General\University\UniversityService;
use Illuminate\Http\Request;

class UniversityController extends Controller
{
    public function __construct(protected UniversityService $universityService)
    {}
          /**
     * @OA\Get(
     *     path="/universities",
     *     tags={"App" , "App - University"},
     *     summary="get all universities",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *    @OA\Parameter(
     *         name="paginate",
     *         in="query",
     *         required=false,
     *         @OA\Schema(enum={"0","1"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/UniversityResource")
     *     )
     * )
     * @OA\Get(
     *     path="/admin/universities",
     *     tags={"Admin" , "Admin - University"},
     *     summary="get all universities",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *    @OA\Parameter(
     *         name="paginate",
     *         in="query",
     *         required=false,
     *         @OA\Schema(enum={"0","1"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/UniversityResource")
     *     )
     * )
     */
    public function index(Request $request)
    {
        authorize('view_university', true);

        try {
            return success($this->universityService->index($request));
        }  catch (\Exception $e) {
            return error($e->getMessage(), [$e->getMessage()], $e->getCode());
        }
    }
}
