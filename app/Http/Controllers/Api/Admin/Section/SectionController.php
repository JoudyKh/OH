<?php

namespace App\Http\Controllers\Api\Admin\Section;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\Section\StoreSectionRequest;
use App\Http\Requests\Api\Admin\Section\UpdateSectionRequest;
use App\Http\Resources\SectionResource;
use App\Models\Section;
use App\Services\Admin\Section\SectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SectionController extends Controller
{
    public function __construct(protected SectionService $sectionService)
    {
    }
    /**
     * @OA\Post(
     *       path="/admin/sections/{type}",
     *       operationId="post-super-section",
     *      tags={"Admin", "Admin - Section"},
     *       security={{ "bearerAuth": {} }},
     *       summary="Store Super Section data",
     *       description="Store Super Section with the provided information",
     *     @OA\Parameter(
     *     name="type",
     *     in="path",
     *      required=true,
     *     description="pass it sub_section ",
     *     @OA\Schema(
     *         enum={"super_section", "library_section"},
     *     )
     *      ),
     *       @OA\RequestBody(
     *           required=true,
     *           description="Section data",
     *               @OA\MediaType(
     *               mediaType="multipart/form-data",
     *               @OA\Schema(
     *               required={"name", "image"},
     *               @OA\Property(property="name", type="string", example="Arabic section name "),
     *               @OA\Property(
     *                      property="image",
     *                      type="string",
     *                      format="binary",
     *                      description="Image file to upload"
     *                  ),
     *              @OA\Property(property="sort_order", type="integer", example="1"),
     *           ),
     *    ),
     *       ),
     *       @OA\Response(
     *           response=201,
     *           description="Section stored successfully",
     *           @OA\JsonContent(
     *               @OA\Property(property="message", type="string", example="Section stored successfully"),
     *           )
     *       ),
     *       @OA\Response(
     *           response=422,
     *           description="Validation error",
     *           @OA\JsonContent(
     *               @OA\Property(property="message", type="string", example="The given data was invalid."),
     *           )
     *       ),
     *  )
     * @OA\Post(
     *      path="/admin/sections/{type}/{parentSection}",
     *      operationId="post-store-section",
     *     tags={"Admin", "Admin - Section"},
     *     @OA\Parameter(
     *     name="type",
     *     in="path",
     *      required=true,
     *     description="pass it sub_section ",
     *     @OA\Schema(
     *         enum={"sub_section", "sub_library_section"}
     *     )
     *      ),
     *     @OA\Parameter(
     *     name="parentSection",
     *     in="path",
     *      required=true,
     *     description="pass the parent section id  ",
     *     @OA\Schema(
     *         type="integer"
     *     )
     *      ),
     *      security={{ "bearerAuth": {} }},
     *      summary="Store sub section data",
     *      description="Store sub section with the provided information",
     *      @OA\RequestBody(
     *          required=true,
     *          description="Section data",
     *              @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *              required={"name","image"},
     *              @OA\Property(property="name", type="string", example="Arabic brand name "),
     *              @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                     description="Image file to upload"
     *                 ),
     *              @OA\Property(property="sort_order", type="integer", example="1"),
     *              @OA\Property(property="sub_type", enum={"lecture","project"}),
     *              @OA\Property(property="is_special", enum={"0","1"}),

     *          ),
     *   ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Section stored successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Section udpated successfully"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *          )
     *      ),
     * )
     */
    public function store(StoreSectionRequest $request, $type, Section $parentSection = null)
    {
        authorize('create_section');

        try {
            return success($this->sectionService->store($request, $parentSection, $type), 201);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], $th->getCode());
        }

    }


    /**
     * @OA\Post(
     *      path="/admin/sections/{id}",
     *      operationId="store-section",
     *     tags={"Admin", "Admin - Section"},
     *     @OA\Parameter(
     *        name="id",
     *        in="path",
     *        description="section id to update ",
     *        required=false,
     *        @OA\Schema(
     *            type="integer"
     *        )
     *      ),
     *     @OA\Parameter(
     *         name="_method",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string", enum={"PUT"}, default="PUT")
     *     ),
     *      security={{ "bearerAuth": {} }},
     *      summary="Update Section data",
     *      description="Update Section with the provided information",
     *      @OA\RequestBody(
     *          required=true,
     *          description="Section data",
     *              @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *              @OA\Property(property="name", type="string", example="Arabic section name "),
     *              @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                     description="Image file to upload"
     *                 ),
     *              @OA\Property(property="is_special", enum={"0","1"}),
     *          ),
     *   ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Section updated successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Section udpated successfully"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *          )
     *      ),
     * )
     */

    public function update(UpdateSectionRequest $request, Section $section)
    {
        authorize('edit_section');

        try {
            return success($this->sectionService->update($request, $section));
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], $th->getCode());
        }

    }

    /**
     * @OA\Delete(
     *     path="/admin/sections/{id}/force",
     *     tags={"Admin", "Admin - Section"},
     *     summary="Delete an section or brand",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the section or brand to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     * 
     * * @OA\Delete(
     *     path="/admin/sections/{id}",
     *     tags={"Admin", "Admin - Section"},
     *     summary="Delete an section or brand",
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the section or brand to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     security={{ "bearerAuth": {} }},
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
    public function delete($id, $force = null)
    {

        try {
            return success($this->sectionService->delete($id, $force));
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()] ,$th->getCode());
        }
    }
    /**
     * @OA\Get(
     *     path="/admin/sections/{id}/restore/section",
     *     tags={"Admin", "Admin - Section"},
     *     summary="Restore a soft-deleted section or brand",
     *     security={{ "bearerAuth": {} }},
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
    public function restore($id)
    {
        authorize('restore_section');

        try {
            return success($this->sectionService->restore($id));
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], $th->getCode());
        }
    }
}
