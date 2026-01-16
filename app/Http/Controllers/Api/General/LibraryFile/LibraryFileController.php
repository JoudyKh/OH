<?php

namespace App\Http\Controllers\Api\General\LibraryFile;

use App\Http\Controllers\Controller;
use App\Models\AdView;
use App\Models\LibraryFile;
use App\Models\Section;
use App\Services\General\LibraryFile\LibraryFileService;
use Illuminate\Http\Request;

class LibraryFileController extends Controller
{
    public function __construct(protected LibraryFileService $libraryFileService)
    {
    }
    /**
     * @OA\Get(
     *     path="/sections/{section}/files/all",
     *     tags={"App" , "App - LibraryFiles"},
     *     summary="get all section LibraryFiles",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *    @OA\Parameter(
     *         name="section",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
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
     *         @OA\Schema(enum={"sub_library_files_view"}),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/LibraryFileResource")
     *     )
     * )
     * @OA\Get(
     *     path="/admin/sections/{section}/files/all",
     *     tags={"Admin" , "Admin - LibraryFiles"},
     *     summary="get all section LibraryFiless",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *    @OA\Parameter(
     *         name="section",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
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
     *    @OA\Parameter(
     *         name="view",
     *         in="query",
     *         required=true,
     *         @OA\Schema(enum={"sub_library_files_view"}),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/LibraryFileResource")
     *     )
     * )
     */
    public function index(Section $section, Request $request)
    {
        authorize('view_library_file', true);

        try {
            $user = auth()->user();

            $user && ($user->hasRole(['admin', 'content_manager', 'project_manager', 'superadmin'])) ?
                $visitor_count = AdView::where('view', $request->input('view'))->where('model_id', $section->id)->count('ip') :
                $visitor_count = visitorsCount($request, $section);
            $result = $this->libraryFileService->index($request, $section);

            return success($result['files'], 200, [
                'visitor_count' => $visitor_count,
                'sub_library_sec' => $section,
                'max_sort_order' => $result['max_sort_order']
            ]);
            // return success($this->libraryFileService->index($request, $section), 200, ['visitor_count' => $visitor_count, 'sub_library_sec' => $section]);
        } catch (\Exception $e) {
            return error($e->getMessage(), [$e->getMessage()], $e->getCode());
        }
    }
    /**
     * @OA\Get(
     *     path="/sections/{section}/files/{file}",
     *     tags={"App" , "App - LibraryFiles"},
     *     summary="show a LibraryFiles",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *    @OA\Parameter(
     *         name="section",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *    @OA\Parameter(
     *         name="file",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/LibraryFileResource")
     *     )
     * )
     * @OA\Get(
     *     path="/admin/sections/{section}/files/{file}",
     *     tags={"Admin" , "Admin - LibraryFiles"},
     *     summary="show a LibraryFiles",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *    @OA\Parameter(
     *         name="section",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *    @OA\Parameter(
     *         name="file",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/LibraryFileResource")
     *     )
     * )
     */
    public function show(Section $section, LibraryFile $file)
    {
        authorize('view_library_file', true);

        try {
            return success($this->libraryFileService->show($section, $file), 200, ['sub_library_sec' => $section]);
        } catch (\Exception $e) {
            return error($e->getMessage(), [$e->getMessage()], $e->getCode());
        }
    }
    public function download(Section $section, LibraryFile $file)
    {
        return $this->libraryFileService->downloadFile($file);
    }
}
