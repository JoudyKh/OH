<?php

namespace App\Http\Controllers\Api\General\Library;

use App\Http\Controllers\Controller;
use App\Models\AdView;
use App\Models\Library;
use App\Models\Section;
use App\Services\General\Library\LibraryService;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function __construct(protected LibraryService $libraryService)
    {
    }
    /**
     * @OA\Get(
     *     path="/libraries/{section}",
     *     tags={"App" , "App - Library"},
     *     summary="get all section libraries",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *    @OA\Parameter(
     *         name="section",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
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
     *   @OA\Parameter(
     *         name="finger_print",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Parameter(
     *         name="view",
     *         in="query",
     *         required=true,
     *         @OA\Schema(enum={"libraries_view"}),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/LibraryResource")
     *     )
     * )
     * @OA\Get(
     *     path="/admin/libraries/{section}",
     *     tags={"Admin" , "Admin - Library"},
     *     summary="get all section libraries",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *    @OA\Parameter(
     *         name="section",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
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
     *     @OA\Parameter(
     *         name="view",
     *         in="query",
     *         required=true,
     *         @OA\Schema(enum={"libraries_view"}),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/LibraryResource")
     *     )
     * )
     */
    public function index(Section $section, Request $request)
    {
        authorize('view_library', true);

        try {
            $user = auth()->user();

            $user && ($user->hasRole(['admin', 'content_manager', 'project_manager', 'superadmin'])) ?
                $visitor_count = AdView::where('view', $request->input('view'))->count('ip') :
                $visitor_count = visitorsCount($request);
            $result = $this->libraryService->index($section, $request);

            return success($result['libraries'], 200, [
                'visitor_count' => $visitor_count,
                'parent_section' => $section,
                'max_sort_order' => $result['max_sort_order']
            ]);
        } catch (\Exception $e) {
            return error($e->getMessage(), [$e->getMessage()], $e->getCode());
        }
    }
    /**
     * @OA\Get(
     *     path="/libraries/{section}/{library}",
     *     tags={"App" , "App - Library"},
     *     summary="show a Library",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *    @OA\Parameter(
     *         name="section",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *    @OA\Parameter(
     *         name="library",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *   @OA\Parameter(
     *         name="finger_print",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Parameter(
     *         name="view",
     *         in="query",
     *         required=true,
     *         @OA\Schema(enum={"one_library_view"}),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/LibraryResource")
     *     )
     * )
     * @OA\Get(
     *     path="/admin/libraries/{section}/{library}",
     *     tags={"Admin" , "Admin - Library"},
     *     summary="show a Library",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *    @OA\Parameter(
     *         name="section",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *    @OA\Parameter(
     *         name="library",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *    @OA\Parameter(
     *         name="view",
     *         in="query",
     *         required=true,
     *         @OA\Schema(enum={"one_library_view"}),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/LibraryResource")
     *     )
     * )
     */
    public function show(Request $request, Section $section, Library $library)
    {
        authorize('view_library', true);

        try {
            $user = auth()->user();

            $user && ($user->hasRole(['admin', 'content_manager', 'project_manager', 'superadmin'])) ?
                $visitor_count = AdView::where('view', $request->input('view'))->where('model_id', $library->id)->count('ip') :
                $visitor_count = visitorsCount($request, $library);
            return success($this->libraryService->show($section, $library), 200, ['visitor_count' => $visitor_count, 'parent_section' => $section]);
        } catch (\Exception $e) {
            return error($e->getMessage(), [$e->getMessage()], $e->getCode());
        }
    }
}
