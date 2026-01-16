<?php

namespace App\Http\Controllers\Api\Admin\GraduationProject;

use App\Http\Controllers\Controller;
use App\Models\GraduationProjectRequest;
use App\Services\Admin\GraduationProject\GraduationProjectService;
use Illuminate\Http\Request;

class GraduationProjectController extends Controller
{
    public function __construct(protected GraduationProjectService $graduationProjectService)
    {
    }
    /**
     * @OA\Get(
     *     path="/admin/graduation-projects",
     *     tags={"Admin" , "Admin - GraduationProject"},
     *     summary="get all GraduationProjects",
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
     *    @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             example="any"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     )
     * )
     */
    public function index(Request $request)
    {
        authorize('view_graduation_project_request');

        try {
            return success($this->graduationProjectService->index($request));
        } catch (\Exception $e) {
            return error($e);
        }
    }
    /**
     * @OA\Get(
     *     path="/admin/graduation-projects/{project}",
     *     tags={"Admin" , "Admin - GraduationProject"},
     *     summary="get all GraduationProject",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *    @OA\Parameter(
     *        name="project",
     *        in="path",
     *        required=true,
     *        @OA\Schema(
     *            type="integer"
     *        )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     )
     * )
     */
    public function show(GraduationProjectRequest $project)
    {
        authorize('view_graduation_project_request');

        try {
            return success($this->graduationProjectService->show($project));
        } catch (\Exception $e) {
            return error($e);
        }
    }
          /**
     * @OA\Delete(
     *     path="/admin/graduation-projects/{id}",
     *     tags={"Admin" , "Admin - GraduationProject"},
     *     summary="SoftDelete an existing GraduationProject",
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
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     * @OA\Delete(
     *     path="/admin/graduation-projects/{id}/force",
     *     tags={"Admin" , "Admin - GraduationProject"},
     *     summary="ForceDelete an existing GraduationProject",
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
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function destroy($project, $force = null)
    {

        try {
            return success($this->graduationProjectService->destroy($project, $force));
        }  catch (\Exception $e) {
            return error($e->getMessage(), [$e->getMessage()], $e->getCode());
        }
    }
        /**
     * @OA\Get(
     *     path="/admin/graduation-projects/{id}/restore",
     *     tags={"Admin", "Admin - GraduationProject"},
     *     summary="Restore a soft-deleted GraduationProject",
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
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function restore($project)
    {
        authorize('restore_graduation_project_request');

        try {
            return success($this->graduationProjectService->restore($project));
        }  catch (\Exception $e) {
            return error($e->getMessage(), [$e->getMessage()], $e->getCode());
        }
    }
}
