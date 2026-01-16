<?php

namespace App\Http\Controllers\Api\General\Interview;

use App\Http\Controllers\Controller;
use App\Models\AdView;
use App\Models\Interview;
use App\Services\General\Inteview\InterviewService;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    public function __construct(protected InterviewService $interviewService)
    {}
          /**
     * @OA\Get(
     *     path="/interviews",
     *     tags={"App" , "App - Interview"},
     *     summary="get all interviews",
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
     *    @OA\Parameter(
     *         name="finger_print",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Parameter(
     *         name="view",
     *         in="query",
     *         required=true,
     *         @OA\Schema(enum={"interviews_view"}),
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         required=false,
     *         @OA\Schema(enum={"digital", "physical"}),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/InterviewResource")
     *     )
     * )
     * @OA\Get(
     *     path="/admin/interviews",
     *     tags={"Admin" , "Admin - Interview"},
     *     summary="get all interviews",
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
     *         name="sort_by",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             example="data base column name"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="view",
     *         in="query",
     *         required=true,
     *         @OA\Schema(enum={"interviews_view"}),
     *     ),
     *    @OA\Parameter(
     *         name="type",
     *         in="query",
     *         required=false,
     *         @OA\Schema(enum={"digital", "physical"}),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/InterviewResource")
     *     )
     * )
     */
    public function index(Request $request)
    {
        authorize('view_interview', true);

        try {
            $user = auth()->user();

            $user && ($user->hasRole(['admin', 'content_manager', 'project_manager', 'superadmin'])) ?
                $visitor_count = AdView::where('view', $request->input('view'))->count('ip') :
                $visitor_count = visitorsCount($request);
                $result = $this->interviewService->index($request);  
    
                return success($result['interviews'], 200, [  
                    'visitor_count' => $visitor_count,  
                    'max_sort_order' => $result['max_sort_order']  
                ]); 
        }  catch (\Exception $e) {
            return error($e->getMessage(), [$e->getMessage()], $e->getCode());
        }
    }
            /**
     * @OA\Get(
     *     path="/interviews/{interview}",
     *     tags={"App" , "App - Interview"},
     *     summary="show a Interview",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *    @OA\Parameter(
     *         name="interview",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *    @OA\Parameter(
     *         name="finger_print",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Parameter(
     *         name="view",
     *         in="query",
     *         required=true,
     *         @OA\Schema(enum={"one_interview_view"}),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/InterviewResource")
     *     )
     * )
     * @OA\Get(
     *     path="/admin/interviews/{interview}",
     *     tags={"Admin" , "Admin - Interview"},
     *     summary="show a Interview",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *    @OA\Parameter(
     *         name="interview",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="view",
     *         in="query",
     *         required=true,
     *         @OA\Schema(enum={"one_interview_view"}),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/InterviewResource")
     *     )
     * )
     */
    public function show(Request $request ,Interview $interview)
    {
        authorize('view_interview', true);

        try {
            $user = auth()->user();

            $user && ($user->hasRole(['admin', 'content_manager', 'project_manager', 'superadmin'])) ?
                $visitor_count = AdView::where('view', $request->input('view'))->where('model_id', $interview->id)->count('ip') :
                $visitor_count = visitorsCount($request, $interview);
            return success($this->interviewService->show($interview), 200, ['visitor_count' => $visitor_count]);
        }  catch (\Exception $e) {
            return error($e->getMessage(), [$e->getMessage()], $e->getCode());
        }
    }
}
