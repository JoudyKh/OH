<?php

namespace App\Http\Controllers\Api\App\InterviewRequest;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\App\InterviewRequest\CreateInterviewRequest;
use App\Models\Interview;
use App\Services\App\InterviewRequest\InterviewRequestService;
use Illuminate\Http\Request;

class InterviewRequestController extends Controller
{
    public function __construct(protected InterviewRequestService $interviewRequestService)
    {
    }
    /**
     * @OA\Post(
     *     path="/interviews/{interview}/requests/{type}",
     *     tags={"App" , "App - Interview - Request"},
     *     summary="create interview request",
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
     *        name="type",
     *        in="path",
     *        required=true,
     *        @OA\Schema(
     *            enum={"electronic_certificate","cartoon_certificate","participation"}
     *        )
     *      ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/CreateInterviewRequest2") ,
     *         )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/InterviewRequestResource")
     *     )
     * )
     */
    public function store(Interview $interview, CreateInterviewRequest $request, $type)
    {
        try {
            return success($this->interviewRequestService->store($interview, $request, $type));
        } catch (\Exception $e) {
            return error($e->getMessage(), [$e->getMessage()], $e->getCode());
        }
    }
}
