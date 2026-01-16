<?php

namespace App\Http\Controllers\Api\App\GraduationProject;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\App\GraduationProject\CreateGraduationProjectRequest;
use App\Services\App\GraduationProject\GraduationProjectService;
use Illuminate\Http\Request;

class GraduationProjectController extends Controller
{
    public function __construct(protected GraduationProjectService $graduationProjectService)
    {}
     /**
     * @OA\Post(
     *     path="/graduation-projects",
     *     tags={"App" , "App - GraduationProject"},
     *     summary="create GraduationProject",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/CreateGraduationProjectRequest") ,
     *         )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     )
     * )
     */
    public function store(CreateGraduationProjectRequest $request)
    {
        try{
            return success($this->graduationProjectService->store($request), 201);
        }catch(\Exception $e){
            return error($e);
        }
    }
}
