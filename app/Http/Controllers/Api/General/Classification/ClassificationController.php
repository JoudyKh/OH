<?php

namespace App\Http\Controllers\Api\General\Classification;

use App\Http\Controllers\Controller;
use App\Services\General\Classification\ClassificationService;
use Illuminate\Http\Request;

class ClassificationController extends Controller
{
    public function __construct(protected ClassificationService $classificationService)
    {}
           /**
     * @OA\Get(
     *     path="/classifications",
     *     tags={"App" , "App - Classification"},
     *     summary="get all classifications",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *     @OA\Response(
     *         response=200,
     *         description="",
     *     ),
     * )
     * @OA\Get(
     *     path="/admin/classifications",
     *     tags={"Admin" , "Admin - Classification"},
     *     summary="get all classifications",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *     @OA\Response(
     *         response=200,
     *         description="Contact message created successfully",
     *     ),
     * )
     */
    public function index(Request $request)
    {
        authorize('view_classification', true);

        try {
            return success($this->classificationService->index($request));
        }  catch (\Exception $e) {
            return error($e->getMessage(), [$e->getMessage()], $e->getCode());
        }
    }
}
