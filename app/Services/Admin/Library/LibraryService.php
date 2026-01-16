<?php

namespace App\Services\Admin\Library;
use App\Http\Requests\Api\Admin\Library\CreateLibraryRequest;
use App\Http\Requests\Api\Admin\Library\UpdateLibraryRequest;
use App\Http\Resources\LibraryResource;
use App\Models\Library;
use App\Models\Section;
use Illuminate\Support\Facades\Storage;

class LibraryService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function store(Section $section, CreateLibraryRequest $request)
    {
        $data = $request->validated();
        if ($section->type !== 'library_section')
            throw new \Exception(__('messages.invalid_parent_id'));
        $data['section_id'] = $section->id;
        $library = Library::create($data);
        if ($request->has('images')) {
            $imagesPath = [];
            
            foreach ($request->file('images') as $image) {
                $uploadedPath = $image->storePublicly('library/images', 'public');
                
                if ($uploadedPath) {
                    $imagesPath[] = [
                        'image' => $uploadedPath,
                        'library_id' => $library->id
                    ];
                }
            }

            if (!empty($imagesPath)) {
                $library->images()->createMany($imagesPath);
            }
        }
        return LibraryResource::make($library->load(['images', 'librarySec']));
    }
    public function update(Section $section, UpdateLibraryRequest $request, Library $library)
    {
        $data = $request->validated();
        $library->update($data);
        if ($request->has('images')) {
            $imagesPath = [];

            foreach ($request->file('images') as $image) {
                $uploadedPath = $image->storePublicly('library/images', 'public');

                if ($uploadedPath) {
                    $imagesPath[] = [
                        'image' => $uploadedPath,
                        'library_id' => $library->id
                    ];
                }
            }

            if (!empty($imagesPath)) {
                $library->images()->createMany($imagesPath);
            }
        }
        if (isset($data['delete_images'])) {
            $imagesToDelete = $library->images()->whereIn('id', $data['delete_images'])->get();
            $totalImages = $library->images()->count();

            if ($totalImages - $imagesToDelete->count() < 1) {
                throw new \Exception(__('messages.can_not_delete_image'), 422);
            }
            foreach ($imagesToDelete as $image) {
                Storage::delete("public/$image->image");
                $library->images()->where('id', $image->id)->delete();
            }
        }
        return LibraryResource::make($library->load(['images', 'librarySec']));
    }
    public function destroy(Section $section, $library, $force = null)
    {
        if ($force) {
            authorize('force_delete_library');

            $library = Library::onlyTrashed()->findOrFail($library);
            foreach ($library->images as $image) {
                if (Storage::disk('public')->exists($image->image)) {
                    Storage::disk('public')->delete($image->image);
                }
            }
            $library->forceDelete();
        } else {
            authorize(['delete_library', 'force_delete_library']);

            $library = Library::where('id', $library)->first();
            $library->delete();
        }
        return true;
    }
    public function restore(Section $section, $library)
    {
        $library = Library::withTrashed()->find($library);
        if ($library && $library->trashed()) {
            $library->restore();
            return true;
        }
        throw new \Exception(__('messages.not_found'), 404);
    }
}
