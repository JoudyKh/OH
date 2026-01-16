<?php

namespace App\Http\Controllers\Api\App\StudentProject;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\App\StudentProject\CreateStuProRequest;
use App\Services\App\StudentProject\StudentProjectService;
use Illuminate\Http\Request;

class StudentProjectController extends Controller
{
    public function __construct(protected StudentProjectService $studentProjectService)
    {
    }
    /**
     * @OA\Get(
     *     path="/students-projects",
     *     tags={"App" , "App - Student - Project"},
     *     summary="get all student projects",
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
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/StudentProjectResource")
     *     )
     * )
     */
    public function index(Request $request)
    {
        return success($this->studentProjectService->index($request));
    }
    /**
     * @OA\Post(
     *     path="/students-projects",
     *     tags={"App" , "App - Student - Project"},
     *     summary="create Student project",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/CreateStuProRequest") ,
     *         )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/StudentProjectResource")
     *     )
     * )
     */
    public function store(CreateStuProRequest $request)
    {
        try {
            return success($this->studentProjectService->store($request));
        } catch (\Exception $e) {
            return error($e);
        }
    }
}
