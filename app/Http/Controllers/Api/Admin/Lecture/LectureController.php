<?php

namespace App\Http\Controllers\Api\Admin\Lecture;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\Lecture\CreateLectureRequest;
use App\Http\Requests\Api\Admin\Lecture\UpdateLectureRequest;
use App\Models\Lecture;
use App\Models\Section;
use App\Services\Admin\Lecture\LectureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LectureController extends Controller
{
    public function __construct(protected LectureService $lectureService)
    {}
    /**
     * @OA\Post(
     *     path="/admin/sub-sections/{section}/{type}",
     *     tags={"Admin" , "Admin - Lecture"},
     *     summary="Create a new Lecture",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *    @OA\Parameter(
     *         name="section",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *    @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         @OA\Schema(enum={"project", "lecture"})
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/CreateLectureRequest") ,
     *         )
     *      ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/LectureResource")
     *     )
     * )
     */
    public function store(Section $section, $type, CreateLectureRequest $request)
    {
        authorize('create_lecture');

        DB::beginTransaction();

        try {
            $data = $this->lectureService->store($section, $request, $type);
            DB::commit();
            Cache::flush();
            return success($data, 201);

        } catch (\Throwable $th) {
            DB::rollBack();
            return error($th->getMessage(), [$th->getMessage()], $th->getCode());
        }
    }
    /**
     * @OA\Post(
     *     path="/admin/sub-sections/{section}/{type}/{lecture}",
     *     tags={"Admin" , "Admin - Lecture"},
     *     summary="update an existing Lecture",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *    @OA\Parameter(
     *         name="section",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         @OA\Schema(enum={"project", "lecture"})
     *     ),
     *    @OA\Parameter(
     *         name="lecture",
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
     *             @OA\Schema(ref="#/components/schemas/UpdateLectureRequest") ,
     *         )
     *      ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/LectureResource")
     *     )
     * )
     */
    public function update(Section $section, $type, UpdateLectureRequest $request, Lecture $lecture)
    {
        authorize('edit_lecture');

        DB::beginTransaction();

        try {
            $data = $this->lectureService->update($section, $request, $type, $lecture);
            DB::commit();
            Cache::flush();
            return success($data, 201);

        } catch (\Throwable $th) {
            DB::rollBack();
            return error($th->getMessage(), [$th->getMessage()], $th->getCode());
        }
    }
      /**
     * @OA\Delete(
     *     path="/admin/sub-sections/{section}/{type}/{id}",
     *     tags={"Admin" , "Admin - Lecture"},
     *     summary="SoftDelete an existing Lecture",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *     @OA\Parameter(
     *         name="section",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *    @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         @OA\Schema(enum={"project", "lecture"})
     *     ),
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
     *     path="/admin/sub-sections/{section}/{type}/{id}/force",
     *     tags={"Admin" , "Admin - Lecture"},
     *     summary="ForceDelete an existing Lecture",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *     @OA\Parameter(
     *         name="section",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         @OA\Schema(enum={"project", "lecture"})
     *     ),
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
    public function destroy(Section $section, $type, $lecture, $force = null)
    {

        try {
            return success($this->lectureService->destroy($section, $type, $lecture, $force));
        }  catch (\Exception $e) {
            return error($e->getMessage(), [$e->getMessage()], $e->getCode());
        }
    }
        /**
     * @OA\Get(
     *     path="/admin/sub-sections/{section}/{type}/{id}/restore",
     *     tags={"Admin", "Admin - Lecture"},
     *     summary="Restore a soft-deleted Lecture",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *    @OA\Parameter(
     *         name="section",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *    @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         @OA\Schema(enum={"project", "lecture"})
     *     ),
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
    public function restore(Section $section, $type, $lecture)
    {
        authorize('restore_lecture');

        try {
            return success($this->lectureService->resotre($section, $type, $lecture));
        }  catch (\Exception $e) {
            return error($e->getMessage(), [$e->getMessage()], $e->getCode());
        }
    }
}
