<?php

namespace App\Http\Controllers\Api\General\Section;

use App\Http\Controllers\Controller;
use App\Http\Resources\SectionResource;
use App\Models\AdView;
use App\Models\Section;
use App\Services\General\Section\SectionService;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function __construct(protected SectionService $sectionService)
    {
    }
    /** 
     * @OA\Get(
     *      path="/sections/{type}",
     *      summary="get super sections data",
     *      tags={"App", "App - Sections"},
     *     security={{ "bearerAuth": {} }},
     *    @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *          enum={"super_section", "library_section", "super_library_section"},
     *        )
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
     *         name="finger_print",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Parameter(
     *         name="view",
     *         in="query",
     *         required=true,
     *         @OA\Schema(enum={"library_sections_view", "super_sections_view", "super_library_section_view"}),
     *     ),
     *      @OA\Parameter(
     *          name="Accept",
     *          in="header",
     *          required=false,
     *          @OA\Schema(
     *              type="string",
     *              example="application/json"
     *          )
     *      ),
     *     @OA\Response(response=200, description="Successful operation"),
     *  )
     *  @OA\Get(
     *     path="/sections/sub_section/{parentSection}",
     *     summary="get sub sections data",
     *     tags={"App", "App - Sections"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *     name="parentSection",
     *     in="path",
     *     description="pass the parent section id ",
     *     required=true,
     *     @OA\Schema(
     *         type="integer"
     *     )
     *      ),
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
     *         name="sub_type",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             enum={"project", "lecture"},
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
     *         @OA\Schema(enum={"lectures_sections_view", "projects_sections_view"}),
     *     ),
     *    @OA\Response(response=200, description="Successful operation"),
     * )
     * @OA\Get(
     *      path="/admin/sections/{type}",
     *      operationId="admin/super-sections",
     *      summary="get super sections data",
     *      tags={"Admin", "Admin - Section"},
     *     security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *    @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *          enum={"super_section", "library_section", "super_library_section"},
     *        )
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
     *   @OA\Parameter(
     *         name="view",
     *         in="query",
     *         required=true,
     *         @OA\Schema(enum={"library_sections_view", "super_sections_view"}),
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *  )
     *  @OA\Get(
     *     path="/admin/sections/sub_section/{parentSection}",
     *     summary="get sub sections data according to the parent section",
     *     tags={"Admin", "Admin - Section"},
     *     @OA\Parameter(
     *     name="parentSection",
     *     in="path",
     *     description="pass the parent section id ",
     *     required=true,
     *     @OA\Schema(
     *         type="integer"
     *     )
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
     *     @OA\Parameter(
     *         name="sub_type",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             enum={"project", "lecture"},
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="view",
     *         in="query",
     *         required=true,
     *         @OA\Schema(enum={"lectures_sections_view", "projects_sections_view"}),
     *     ),
     *    security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *    @OA\Response(response=200, description="Successful operation"),
     * )
     *  @OA\Get(
     *     path="/admin/sections/sub_section/all",
     *     summary="get all sub sections data",
     *     tags={"Admin", "Admin - Section"},
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
     *         name="sub_type",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             enum={"project", "lecture"},
     *         )
     *     ),
     *    security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *    @OA\Response(response=200, description="Successful operation"),
     * )
     */

    public function index(Request $request, $type, $parentSection = null)
    {
        authorize('view_section', true);
        $librarySection = null;
        $user = auth()->user();
        
        if ($parentSection && $parentSection != 'all') {
            $parentSection = Section::find($parentSection);
        } else if ($type === 'super_library_section') {
            $librarySection = Section::where('type', 'super_library_section')->first();
        }
        $user && ($user->hasRole(['admin', 'content_manager', 'project_manager', 'superadmin'])) ?
            $visitor_count = AdView::where('view', $request->input('view'))
                                        ->where('model_id', $parentSection->id ?? null)
                                        ->count('ip') :
            $visitor_count = visitorsCount($request, $parentSection ?? null);
        $result = $this->sectionService->getAll($request, $parentSection, $type);

        return success($result['sections'], 200, [
            'visitor_count' => $visitor_count,
            'parent_section' => $parentSection ?? $librarySection,
            'max_sort_order' => $result['max_sort_order'] 
        ]);
    }

    /**
     * @OA\Get(
     *      path="/sections/detail/{section_id}",
     *      operationId="app/section",
     *      summary="get section data ",
     *      tags={"App", "App - Sections"},
     *       @OA\Parameter(
     *      name="section_id",
     *      in="path",
     *      description="pass the section ",
     *      required=true,
     *      @OA\Schema(
     *          type="integer"
     *      )
     *       ),
     *     security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Locale of the branch data (optional)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"none", "ar", "en"})
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *  )
     *
     * @OA\Get(
     *     path="/admin/sections/detail/{section_id}/show/one-section",
     *     operationId="admin/section",
     *     summary="get section data ",
     *     tags={"Admin", "Admin - Section"},
     *      @OA\Parameter(
     *     name="section_id",
     *     in="path",
     *     description="pass the section ",
     *     required=true,
     *     @OA\Schema(
     *         type="integer"
     *     )
     *      ),
     *    security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Locale of the branch data (optional)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"none", "ar", "en"})
     *     ),
     *    @OA\Response(response=200, description="Successful operation"),
     * )
     */
    public function show(Section $section)
    {
        authorize('view_section', true);

        return success($this->sectionService->show($section));
    }

}
