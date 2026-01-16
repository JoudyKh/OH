<?php

namespace App\Http\Controllers\Api\General\Lecture;

use App\Http\Controllers\Controller;
use App\Models\AdView;
use App\Models\Lecture;
use App\Models\LectureAttachedFile;
use App\Models\Section;
use App\Services\General\Lecture\LectureService;
use Illuminate\Http\Request;

class LectureController extends Controller
{
    public function __construct(protected LectureService $lectureService)
    {
    }
    /**
     * @OA\Get(
     *     path="/sub-sections/{section}/{type}",
     *     tags={"App" , "App - Lecture"},
     *     summary="get all section lectures",
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
     *         @OA\Schema(enum={"lectures_view", "projects_view"}),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/LectureResource")
     *     )
     * )
     * @OA\Get(
     *     path="/admin/sub-sections/{section}/{type}",
     *     tags={"Admin" , "Admin - Lecture"},
     *     summary="get all section lectures",
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
     *         @OA\Schema(enum={"lectures_view", "projects_view"}),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/LectureResource")
     *     )
     * )
     */
    public function index(Section $section, $type, Request $request)
    {
        authorize('view_lecture', true);

        try {
            $user = auth()->user();
            
            $user && ($user->hasRole(['admin', 'content_manager', 'project_manager', 'superadmin'])) ?
                $visitor_count = AdView::where('view', $request->input('view'))->count('ip') :
                $visitor_count = visitorsCount($request);
                $result = $this->lectureService->index($section, $request, $type);

                return success($result['lectures'], 200, [
                    'visitor_count' => $visitor_count,
                    'sub_section' => $section,
                    'super_section' => $section->superSection,
                    'max_sort_order' => $result['max_sort_order'] 
                ]);
            // return success($this->lectureService->index($section, $request, $type), 200, ['visitor_count' => $visitor_count, 'sub_section' => $section, 'super_section' => $section->superSection]);
        } catch (\Exception $e) {
            return error($e->getMessage(), [$e->getMessage()], $e->getCode());
        }
    }
    /**
     * @OA\Get(
     *     path="/sub-sections/{section}/{type}/{lecture}",
     *     tags={"App" , "App - Lecture"},
     *     summary="show a Lecture",
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
     *    @OA\Parameter(
     *         name="lecture",
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
     *         @OA\Schema(enum={"one_lecture_view", "one_project_view"}),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/LectureResource")
     *     )
     * )
     * @OA\Get(
     *     path="/admin/sub-sections/{section}/{type}/{lecture}",
     *     tags={"Admin" , "Admin - Lecture"},
     *     summary="show a Lecture",
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
     *    @OA\Parameter(
     *         name="lecture",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *    @OA\Parameter(
     *         name="view",
     *         in="query",
     *         required=true,
     *         @OA\Schema(enum={"one_lecture_view", "one_project_view"}),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/LectureResource")
     *     )
     * )
     */
    public function show(Request $request, Section $section, $type, Lecture $lecture)
    {
        authorize('view_lecture', true);

        try {
            $user = auth()->user();

            $user && ($user->hasRole(['admin', 'content_manager', 'project_manager', 'superadmin'])) ?
                $visitor_count = AdView::where('view', $request->input('view'))->where('model_id', $lecture->id)->count('ip') :
                $visitor_count = visitorsCount($request, $lecture);
            return success($this->lectureService->show($section, $type, $lecture), 200, ['visitor_count' => $visitor_count, 'sub_section' => $section, 'super_section' => $section->superSection]);
        } catch (\Exception $e) {
            return error($e->getMessage(), [$e->getMessage()], $e->getCode());
        }
    }
    public function download( Section $section, $type, Lecture $lecture, LectureAttachedFile $file)
    {

        return $this->lectureService->downloadFile($section, $type, $lecture,$file);

    }
      /**
     * @OA\Get(
     *     path="/lectures",
     *     tags={"App" , "App - Lecture"},
     *     summary="show a Lecture",
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
     *         response=200,
     *         description="Successful operation",
     *     )
     * )
     * */
    public function search(Request $request)
    {
        authorize('view_lecture', true);

        try {
            return success($this->lectureService->search($request));
        } catch (\Exception $e) {
            return error($e->getMessage(), [$e->getMessage()], $e->getCode());
        }
    }
}
