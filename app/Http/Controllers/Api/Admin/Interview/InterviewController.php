<?php

namespace App\Http\Controllers\Api\Admin\Interview;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\Interview\CreateInterviewRequest;
use App\Http\Requests\Api\Admin\Interview\UpdateInterviewRequest;
use App\Models\Interview;
use App\Services\Admin\Inteview\InterviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class InterviewController extends Controller
{
    public function __construct(protected InterviewService $interviewService)
    {}
    /**
     * @OA\Post(
     *     path="/admin/interviews",
     *     tags={"Admin" , "Admin - Interview"},
     *     summary="Create a new Interview",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/CreateInterviewRequest") ,
     *         )
     *      ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/InterviewResource")
     *     )
     * )
     */
    public function store(CreateInterviewRequest $request)
    {
        authorize('create_interview');
        DB::beginTransaction();

        try {
            $data = $this->interviewService->store($request);
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
     *     path="/admin/interviews/{interview}",
     *     tags={"Admin" , "Admin - Interview"},
     *     summary="update an existing Interview",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *    @OA\Parameter(
     *         name="interview",
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
     *             @OA\Schema(ref="#/components/schemas/UpdateInterviewRequest") ,
     *         )
     *      ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/InterviewResource")
     *     )
     * )
     */
    public function update(UpdateInterviewRequest $request, Interview $interview)
    {
        authorize('edit_interview');
        DB::beginTransaction();

        try {
            $data = $this->interviewService->update($request, $interview);
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
     *     path="/admin/interviews/{id}",
     *     tags={"Admin" , "Admin - Interview"},
     *     summary="SoftDelete an existing Interview",
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
     *     path="/admin/interviews/{id}/force",
     *     tags={"Admin" , "Admin - Interview"},
     *     summary="ForceDelete an existing Interview",
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
    public function destroy($interview, $force = null)
    {
        try {
            return success($this->interviewService->destroy($interview, $force));
        }  catch (\Exception $e) {
            return error($e->getMessage(), [$e->getMessage()], $e->getCode());
        }
    }
        /**
     * @OA\Get(
     *     path="/admin/interviews/{id}/restore",
     *     tags={"Admin", "Admin - Interview"},
     *     summary="Restore a soft-deleted Interview",
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
    public function restore($interview)
    {
        authorize('restore_interview');
        try {
            return success($this->interviewService->restore($interview));
        }  catch (\Exception $e) {
            return error($e->getMessage(), [$e->getMessage()], $e->getCode());
        }
    }
}
