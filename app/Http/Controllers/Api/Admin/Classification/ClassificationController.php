<?php

namespace App\Http\Controllers\Api\Admin\Classification;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\Classification\CreateClassificationRequest;
use App\Http\Requests\Api\Admin\Classification\UpdateClassificationRequest;
use App\Models\Classification;
use App\Services\Admin\Classification\ClassificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ClassificationController extends Controller
{
    public function __construct(protected ClassificationService $classificationService)
    {}
    /**
     * @OA\Post(
     *     path="/admin/classifications",
     *     tags={"Admin" , "Admin - Classification"},
     *     summary="Create a new Classification",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/CreateClassificationRequest") ,
     *         )
     *      ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful operation",
     *     )
     * )
     */
    public function store(CreateClassificationRequest $request)
    {
        authorize('create_classification');
        DB::beginTransaction();

        try {
            $data = $this->classificationService->store($request);
            DB::commit();
            Cache::flush();
            return success($data, 201);

        } catch (\Throwable $th) {
            DB::rollBack();
            return error($th->getMessage(), [$th->getMessage()], $th->getCode());
        }
    }
    /**
     * @OA\Post(
     *     path="/admin/classifications/{classification}",
     *     tags={"Admin" , "Admin - Classification"},
     *     summary="update an existing Classification",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *    @OA\Parameter(
     *         name="classification",
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
     *             @OA\Schema(ref="#/components/schemas/UpdateClassificationRequest") ,
     *         )
     *      ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful operation",
     *     )
     * )
     */
    public function update(UpdateClassificationRequest $request, Classification $Classification)
    {
        authorize('edit_classification');

        DB::beginTransaction();

        try {
            $data = $this->classificationService->update($request, $Classification);
            DB::commit();
            Cache::flush();
            return success($data, 201);

        } catch (\Throwable $th) {
            DB::rollBack();
            return error($th->getMessage(), [$th->getMessage()], $th->getCode());
        }
    }
      /**
     * @OA\Delete(
     *     path="/admin/classifications/{id}",
     *     tags={"Admin" , "Admin - Classification"},
     *     summary="SoftDelete an existing Classification",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
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
    public function destroy($Classification)
    {
        authorize('delete_classification');

        try {
            return success($this->classificationService->destroy($Classification));
        }  catch (\Exception $e) {
            return error($e->getMessage(), [$e->getMessage()], $e->getCode());
        }
    }
    
}
