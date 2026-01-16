<?php

namespace App\Http\Controllers\Api\Admin\HomeSlider;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\HomeSlider\CreateHomeSliderRequest;
use App\Http\Requests\Api\Admin\HomeSlider\UpdateHomeSliderRequest;
use App\Models\HomeSlider;
use App\Services\Admin\HomeSlider\HomeSliderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeSliderController extends Controller
{
    public function __construct(protected HomeSliderService $homeSliderService)
    {}

    /**
     * @OA\Post(
     *     path="/admin/sliders",
     *     tags={"Admin" , "Admin - Slider"},
     *     summary="Create a new Slider",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/CreateHomeSliderRequest") ,
     *         )
     *      ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful operation",
     *     )
     * )
     */
    public function store(CreateHomeSliderRequest $request)
    {
        authorize('create_home_slider');
        DB::beginTransaction();

        try {
            $data = $this->homeSliderService->store($request);
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
     *     path="/admin/sliders/{id}",
     *     tags={"Admin" , "Admin - Slider"},
     *     summary="update an existing Slider",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *     @OA\Parameter(
     *         name="id",
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
     *             @OA\Schema(ref="#/components/schemas/UpdateHomeSliderRequest") ,
     *         )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     )
     * )
     */
    public function update(UpdateHomeSliderRequest $request, HomeSlider $Slider)
    {
        authorize('edit_home_slider');
        DB::beginTransaction();

        try {
        
            $data = $this->homeSliderService->update($request, $Slider);
            DB::commit();   
            Cache::flush();
            return success($data);

        } catch (\Throwable $th) {
            DB::rollBack();
            return error($th->getMessage(), [$th->getMessage()], $th->getCode());
        }

    }
      /**
     * @OA\Delete(
     *     path="/admin/sliders/{slider}",
     *     tags={"Admin" , "Admin - Slider"},
     *     summary="SoftDelete an existing Slider",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *     @OA\Parameter(
     *         name="slider",
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
    public function destroy(HomeSlider $slider)
    {
        authorize('delete_home_slider');
        DB::beginTransaction();

        try {
            $data = $this->homeSliderService->destroy($slider);
            DB::commit();   
            Cache::flush();
            return success($data);

        } catch (\Throwable $th) {
            DB::rollBack();
            return error($th->getMessage(), [$th->getMessage()], $th->getCode());
        }
    }
}
