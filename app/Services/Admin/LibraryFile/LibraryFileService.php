<?php

namespace App\Services\Admin\LibraryFile;
use App\Http\Requests\Api\Admin\LibraryFile\CreateLibraryFileRequest;
use App\Http\Requests\Api\Admin\LibraryFile\UpdateLibraryFileRequest;
use App\Http\Resources\LibraryFileResource;
use App\Models\LibraryFile;
use App\Models\Section;
use Illuminate\Support\Facades\Storage;

class LibraryFileService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function store(Section $section, CreateLibraryFileRequest $request)
    {
        $data = $request->validated();
        if ($section->type !== 'sub_library_section')
            throw new \Exception(__('messages.invalid_parent_id'));
        $data['sub_library_id'] = $section->id;
        if ($request->hasFile('image')) {
            $data['image'] = $data['image']->storePublicly('library-section/images', 'public');
        }
        if ($request->hasFile('file')) {
            $data['file'] = $data['file']->storePublicly('library-section/files', 'public');
        }
        $file = LibraryFile::create($data);

        return LibraryFileResource::make($file);
    }
    public function update(Section $section, UpdateLibraryFileRequest $request, LibraryFile $file)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            if (Storage::exists("public/{$file->image}")) {
                Storage::delete("public/{$file->image}");
            }
            $data['image'] = $data['image']->storePublicly('library-section/images', 'public');
        }
        if ($request->hasFile('file')) {
            if (Storage::exists("public/{$file->file}")) {
                Storage::delete("public/{$file->file}");
            }
            $data['file'] = $data['file']->storePublicly('library-section/files', 'public');
        }
        $file->update($data);
        return LibraryFileResource::make($file);
    }
    public function destroy(Section $section, LibraryFile $file)
    {
        if (Storage::exists("public/{$file->image}")) {
            Storage::delete("public/{$file->image}");
        }
        if (Storage::exists("public/{$file->file}")) {
            Storage::delete("public/{$file->file}");
        }
        $file->delete();

        return true;
    }
}
