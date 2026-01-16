<?php

namespace App\Http\Controllers\Api\Admin\Admins;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\Admins\CreateAdminRequest;
use App\Http\Requests\Api\Admin\Admins\UpdateAdminRequest;
use App\Models\User;
use App\Services\Admin\Admins\AdminsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminsController extends Controller
{
    public function __construct(protected AdminsService $adminsService)
    {}
    /**
     * @OA\Get(
     *     path="/admin/admins",
     *     tags={"Admin" , "Admin - Admins"},
     *     summary="get all admins",
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
     *         name="role",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             enum={"content_manager","project_manager"},
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     )
     * )
     */
    public function index(Request $request)
    {
        authorize('view_user');
        try {
            return success($this->adminsService->index($request));
        }  catch (\Exception $e) {
            return error($e->getMessage(), [$e->getMessage()], $e->getCode());
        }
    }
     /**
     * @OA\Get(
     *     path="/admin/admins/{admin}",
     *     tags={"Admin" , "Admin - Admins"},
     *     summary="show one Admins",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *     @OA\Parameter(
     *         name="admin",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     )
     * )
     */
    public function show(User $admin)
    {
        authorize('view_user');
        try {
            return success($this->adminsService->show($admin));
        }  catch (\Exception $e) {
            return error($e->getMessage(), [$e->getMessage()], $e->getCode());
        }
    }
    /**
     * @OA\Post(
     *     path="/admin/admins",
     *     tags={"Admin" , "Admin - Admins"},
     *     summary="Create a new Admins",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/CreateAdminRequest") ,
     *         )
     *      ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     )
     * )
     */
    public function store(CreateAdminRequest $request)
    {
        authorize('create_user');
        DB::beginTransaction();

        try {
            $data = $this->adminsService->store($request);
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
     *     path="/admin/admins/{admin}",
     *     tags={"Admin" , "Admin - Admins"},
     *     summary="update an existing Admins",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *     @OA\Parameter(
     *         name="admin",
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
     *             @OA\Schema(ref="#/components/schemas/UpdateAdminRequest") ,
     *         )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     )
     * )
     */
    public function update(UpdateAdminRequest $request, User $admin)
    {
        authorize('edit_user');
        DB::beginTransaction();

        try {
            $data = $this->adminsService->update($request, $admin);
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
     *     path="/admin/admins/{id}",
     *     tags={"Admin" , "Admin - Admins"},
     *     summary="SoftDelete an existing Admins",
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
     * @OA\Delete(
     *     path="/admin/admins/{id}/force",
     *     tags={"Admin" , "Admin - Admins"},
     *     summary="ForceDelete an existing Admins",
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
    public function destroy($admin, $force = null)
    {
        
        DB::beginTransaction();

        try {
            $data = $this->adminsService->destroy($admin, $force);
            DB::commit();   
            Cache::flush();
            return success($data);

        } catch (\Throwable $th) {
            DB::rollBack();
            return error($th->getMessage(), [$th->getMessage()], $th->getCode());
        }
    }
        /**
     * @OA\Get(
     *     path="/admin/admins/{id}/restore",
     *     tags={"Admin", "Admin - Admins"},
     *     summary="Restore a soft-deleted Admins",
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
    public function restore($admin)
    {
        authorize('restore_user');
        try {
            return success($this->adminsService->restore($admin));
        }  catch (\Exception $e) {
            return error($e->getMessage(), [$e->getMessage()], $e->getCode());
        }
    }
}
