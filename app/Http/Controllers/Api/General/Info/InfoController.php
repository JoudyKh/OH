<?php

namespace App\Http\Controllers\Api\General\Info;

use App\Models\AdView;
use App\Models\Section;
use App\Http\Controllers\Controller;
use App\Http\Resources\SectionResource;
use App\Services\General\Info\InfoService as GeneralInfoService;
use Illuminate\Http\Request;


class InfoController extends Controller
{
    public function __construct(protected GeneralInfoService $infoService)
    {
    }

    /**
     * @OA\Get(
     *     path="/infos",
     *     tags={"App" , "App - Info"},
     *     summary="Get all info",
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *      security={{ "bearerAuth": {} }},
     *      @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             enum={"en", "ar"}
     *         ),
     *         description="The locale of the response"
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
     *         @OA\Schema(enum={"terms_view", "privacy_view", "contact_us_view"}),
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     )
     * )
     * @OA\Get(
     *     path="/admin/infos",
     *     tags={"Admin" , "Admin - Info"},
     *     summary="Get all admin info",
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     )
     * )
     */
    public function index(Request $request)
    {
        authorize('view_info', true);

        $user = auth()->user();
        $visitor_count = 0;

        if ($request->view !== 'home') {
            $user && ($user->hasRole(['admin', 'content_manager', 'project_manager', 'superadmin'])) ?
                $visitor_count = AdView::where('view', $request->input('view'))->count('ip') :
                $visitor_count = visitorsCount($request);
        }

        return success($this->infoService->getAll(), 200, ['visitor_count' => $visitor_count]);
    }


}
