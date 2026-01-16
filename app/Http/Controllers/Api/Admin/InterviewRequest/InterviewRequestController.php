<?php

namespace App\Http\Controllers\Api\Admin\InterviewRequest;

use App\Http\Controllers\Controller;
use App\Http\Resources\InterviewRequestResource;
use App\Models\Interview;
use App\Models\InterviewRequest;
use App\Services\Admin\InterviewRequest\InterviewRequestService;
use Illuminate\Http\Request;

class InterviewRequestController extends Controller
{
    public function __construct(protected InterviewRequestService $interviewRequestService)
    {}
     /**
     * @OA\Get(
     *     path="/admin/interviews/{interview}/requests",
     *     tags={"Admin" , "Admin - Interview - Request"},
     *     summary="get all interview requests",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *    @OA\Parameter(
     *        name="interview",
     *        in="path",
     *        required=true,
     *        @OA\Schema(
     *            type="integer"
     *        )
     *      ),
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
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/InterviewRequestResource")
     *     )
     * )
     */
    public function index(Request $request, Interview $interview)
    {
        authorize('view_interview_request');
        return success($this->interviewRequestService->index($request, $interview));
    }
     /**
     * @OA\Get(
     *     path="/admin/interviews/{interview}/requests/{request}",
     *     tags={"Admin" , "Admin - Interview - Request"},
     *     summary="get all interview requests",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *    @OA\Parameter(
     *        name="interview",
     *        in="path",
     *        required=true,
     *        @OA\Schema(
     *            type="integer"
     *        )
     *      ),
     *    @OA\Parameter(
     *        name="request",
     *        in="path",
     *        required=true,
     *        @OA\Schema(
     *            type="integer"
     *        )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/InterviewRequestResource")
     *     )
     * )
     */
    public function show(Interview $interview, InterviewRequest $request)
    {
        authorize('view_interview_request');

        return success(InterviewRequestResource::make($request->load(['interview', 'university'])));
    }
          /**
     * @OA\Delete(
     *     path="/admin/interviews/{interview}/requests/{id}",
     *     tags={"Admin" , "Admin - Interview - Request"},
     *     summary="SoftDelete an existing request",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *     @OA\Parameter(
     *         name="interview",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
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
     *     path="/admin/interviews/{interview}/requests/{id}/force",
     *     tags={"Admin" , "Admin - Interview - Request"},
     *     summary="ForceDelete an existing Library",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *     @OA\Parameter(
     *         name="section",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
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
    public function destroy(Interview $interview, $request, $force = null)
    {

        try {
            return success($this->interviewRequestService->destroy($interview, $request, $force));
        }  catch (\Exception $e) {
            return error($e->getMessage(), [$e->getMessage()], $e->getCode());
        }
    }
        /**
     * @OA\Get(
     *     path="/admin/interviews/{interview}/requests/{id}/restore",
     *     tags={"Admin", "Admin - Interview - Request"},
     *     summary="Restore a soft-deleted request",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *    @OA\Parameter(
     *         name="interview",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
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
    public function restore(Interview $interview, $request)
    {
        authorize('restore_interview_request');

        try {
            return success($this->interviewRequestService->restore($interview, $request));
        }  catch (\Exception $e) {
            return error($e->getMessage(), [$e->getMessage()], $e->getCode());
        }
    }
}
