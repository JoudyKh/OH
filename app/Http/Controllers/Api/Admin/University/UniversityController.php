<?php

namespace App\Http\Controllers\Api\Admin\University;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\University\CreateUniversityRequest;
use App\Http\Requests\Api\Admin\University\UpdateUniversityRequest;
use App\Models\University;
use App\Services\Admin\University\UniversityService;


class UniversityController extends Controller
{
    public function __construct(protected UniversityService $universityService)
    {}
    /**
     * @OA\Post(
     *     path="/admin/universities",
     *     tags={"Admin" , "Admin - University"},
     *     summary="Create a new University",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/CreateUniversityRequest") ,
     *         )
     *      ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/UniversityResource")
     *     )
     * )
     */
    public function store(CreateUniversityRequest $request)
    {
        authorize('create_university');
        try {
            $data = $this->universityService->store($request);
            return success($data, 201);

        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], $th->getCode());
        }
    }
    /**
     * @OA\Post(
     *     path="/admin/universities/{university}",
     *     tags={"Admin" , "Admin - University"},
     *     summary="update an existing University",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *    @OA\Parameter(
     *         name="university",
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
     *             @OA\Schema(ref="#/components/schemas/UpdateUniversityRequest") ,
     *         )
     *      ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/UniversityResource")
     *     )
     * )
     */
    public function update(UpdateUniversityRequest $request, University $university)
    {
        authorize('edit_university');

        try {
            $data = $this->universityService->update($request, $university);
            return success($data, 201);

        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], $th->getCode());
        }
    }
      /**
     * @OA\Delete(
     *     path="/admin/universities/{university}",
     *     tags={"Admin" , "Admin - University"},
     *     summary="SoftDelete an existing University",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *     @OA\Parameter(
     *         name="university",
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
    public function destroy(University $university)
    {
        authorize('delete_university');
        try {
            return success($this->universityService->destroy($university));
        }  catch (\Exception $e) {
            return error($e->getMessage(), [$e->getMessage()], $e->getCode());
        }
    }
}
