<?php

namespace App\Http\Controllers\Api\Admin\LibraryFile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\LibraryFile\CreateLibraryFileRequest;
use App\Http\Requests\Api\Admin\LibraryFile\UpdateLibraryFileRequest;
use App\Models\LibraryFile;
use App\Models\Section;
use App\Services\Admin\LibraryFile\LibraryFileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LibraryFileController extends Controller
{
    public function __construct(protected LibraryFileService $libraryFileService)
    {}
    /**
     * @OA\Post(
     *     path="/admin/sections/{section}/files/store",
     *     tags={"Admin" , "Admin - LibraryFiles"},
     *     summary="Create a new LibraryFiles",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *    @OA\Parameter(
     *         name="section",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/CreateLibraryFileRequest") ,
     *         )
     *      ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/LibraryFileResource")
     *     )
     * )
     */
    public function store(Section $section, CreateLibraryFileRequest $request)
    {
        authorize('create_library_file');

        DB::beginTransaction();

        try {
            $data = $this->libraryFileService->store($section, $request);
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
     *     path="/admin/sections/{section}/files/{file}",
     *     tags={"Admin" , "Admin - LibraryFiles"},
     *     summary="update an existing LibraryFiles",
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
     *             @OA\Schema(ref="#/components/schemas/UpdateLibraryFileRequest") ,
     *         )
     *      ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/LibraryFileResource")
     *     )
     * )
     */
    public function update(Section $section, UpdateLibraryFileRequest $request, LibraryFile $file)
    {
        authorize('edit_library_file');

        DB::beginTransaction();

        try {
            $data = $this->libraryFileService->update($section, $request, $file);
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
     *     path="/admin/sections/{section}/files/{file}",
     *     tags={"Admin" , "Admin - LibraryFiles"},
     *     summary="SoftDelete an existing LibraryFiles",
     *      security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *     @OA\Parameter(
     *         name="section",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="file",
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
    public function destroy(Section $section, LibraryFile $file)
    {
        authorize('delete_library_file');

        try {
            return success($this->libraryFileService->destroy($section, $file));
        }  catch (\Exception $e) {
            return error($e->getMessage(), [$e->getMessage()], $e->getCode());
        }
    }
}
