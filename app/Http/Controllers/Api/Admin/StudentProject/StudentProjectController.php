<?php

namespace App\Http\Controllers\Api\Admin\StudentProject;

use App\Exports\StudentProjectExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\StudentProject\UpdateStuProStatusRequest;
use App\Http\Resources\StudentProjectResource;
use App\Models\StudentProject;
use App\Models\StudentProjectFile;
use App\Services\Admin\StudentProject\StudentProjectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class StudentProjectController extends Controller
{
    public function __construct(protected StudentProjectService $studentProjectService)
    {
    }
    /**
     * @OA\Get(
     *     path="/admin/students-projects",
     *     tags={"Admin" , "Admin - Student - Project"},
     *     summary="get all students projects",
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
     *         name="status",
     *         in="query",
     *         required=true,
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
        authorize('view_student_project');

        return success($this->studentProjectService->index($request));
    }
    /**
     * @OA\Get(
     *     path="/admin/students-projects/{project}",
     *     tags={"Admin", "Admin - Student - Project"},
     *     summary="shoe student project",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *     @OA\Parameter(
     *         name="project",
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
    public function show(StudentProject $project)
    {
        authorize('view_student_project');

        return success(StudentProjectResource::make($project->load('files')));
    }


    public function download( StudentProjectFile $file)
    {
        authorize('view_student_project');

        return $this->studentProjectService->downloadFile($file);

    }
    /**
     * @OA\Post(
     *     path="/admin/students-projects/{id}",
     *     tags={"Admin" , "Admin - Student - Project"},
     *     summary="update an existing Student project",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="_method",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string", enum={"PUT"}, default="PUT")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/UpdateStuProStatusRequest") ,
     *         )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/StudentProjectResource")
     *     )
     * )
     */
    public function update(UpdateStuProStatusRequest $request, StudentProject $project)
    {
        authorize('edit_student_project');

        return success($this->studentProjectService->update($request, $project));
    }
    /**
     * @OA\Delete(
     *     path="/admin/students-projects/{id}",
     *     tags={"Admin" , "Admin - Student - Project"},
     *     summary="SoftDelete an existing Student project",
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
     *     path="/admin/students-projects/{id}/force",
     *     tags={"Admin" , "Admin - Student - Project"},
     *     summary="ForceDelete an existing Student project",
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

        DB::beginTransaction();

        try {
            $data = $this->studentProjectService->destroy($project, $force);
            DB::commit();
            Cache::flush();
            return success($data);

        } catch (\Throwable $th) {
            DB::rollBack();
            return error($th->getMessage(), [$th->getMessage()], $th->getCode());
        }
    }
    /**
     * @OA\Get(
     *     path="/admin/students-projects/{id}/restore/project",
     *     tags={"Admin", "Admin - Student - Project"},
     *     summary="Restore a soft-deleted Student project",
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
        authorize('restore_student_project');

        try {
            return success($this->studentProjectService->restore($project));
        } catch (\Exception $e) {
            return error($e->getMessage(), [$e->getMessage()], $e->getCode());
        }
    }
    /**
     * @OA\Get(
     *     path="/admin/students-projects/export/excel",
     *     tags={"Admin" , "Admin - Student - Project"},
     *     summary="get a xlsx file",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *    @OA\Parameter(
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
     *         response=201,
     *         description="Successful operation",
     *     )
     * )
     */
    public function exportExcel(Request $request)
    {
        authorize('view_student_project');

        try {
            return Excel::download(new StudentProjectExport($request), 'مشاريع الطلاب.xlsx');
        } catch (\Exception $e) {
            return error($e->getMessage(), [$e->getMessage()], $e->getCode());
        }
    }
    /**
     * @OA\Get(
     *     path="/admin/students-projects/export/pdf",
     *     tags={"Admin" , "Admin - Student - Project"},
     *     summary="get a xlsx file",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *    @OA\Parameter(
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
     *         response=201,
     *         description="Successful operation",
     *     )
     * )
     */
    public function exportPdf(Request $request)
    {
        authorize('view_student_project');

        try {
            return $this->studentProjectService->exportPdf($request);
        } catch (\Exception $e) {
            return error($e->getMessage(), [$e->getMessage()], $e->getCode());
        }
    }
}
