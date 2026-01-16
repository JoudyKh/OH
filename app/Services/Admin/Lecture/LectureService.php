<?php

namespace App\Services\Admin\Lecture;
use App\Http\Requests\Api\Admin\Lecture\CreateLectureRequest;
use App\Http\Requests\Api\Admin\Lecture\UpdateLectureRequest;
use App\Http\Resources\LectureResource;
use App\Models\Lecture;
use App\Models\Section;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class LectureService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function store(Section $section, CreateLectureRequest $request, $type)
    {
        $data = $request->validated();
        if ($section->type !== 'sub_section')
            throw new \Exception(__('messages.invalid_parent_id'));
        $data['sub_section_id'] = $section->id;
        if ($request->hasFile('requirements_image')) {
            $originalFileName = $data['requirements_image']->getClientOriginalName();

            // Create a timestamp  
            $timestamp = now()->timestamp; // or use now()->format('YmdHis') for a formatted string  

            // Create a new file name with timestamp and original file name  
            $newFileName = $timestamp . '_' . $originalFileName;
            $data['requirements_image'] = $data['requirements_image']->storePubliclyAs('lectures/images', $newFileName, 'public');
        }
        $lecture = Lecture::create($data);
        if ($request->has('links')) {
            $linksData = array_map(function ($link) use ($lecture) {
                return [
                    'name' => $link['name'],
                    'link' => $link['link'],
                    'lecture_id' => $lecture->id
                ];
            }, $request->validated('links'));

            if (!empty($linksData)) {
                $lecture->links()->createMany($linksData);
            }
        }
        if ($request->has('images')) {
            $imagesPath = array_map(function ($image) use ($lecture) {
                // Get the original file name  
                $originalFileName = $image->getClientOriginalName();

                // Create a timestamp  
                $timestamp = now()->timestamp; // or use now()->format('YmdHis') for a formatted string  

                // Create a new file name with timestamp and original file name  
                $newFileName = $timestamp . '_' . $originalFileName;

                // Store the image with the new file name  
                return [
                    'image' => $image->storePubliclyAs('lectures/images', $newFileName, 'public'),
                    'lecture_id' => $lecture->id
                ];
            }, $request->file('images'));

            if (!empty($imagesPath)) {
                $lecture->images()->createMany($imagesPath);
            }
        }
        if ($request->has('files')) {
            $filesPath = array_map(function ($file) use ($lecture) {
                return [
                    'file' => $file['file']->storePublicly('lectures/files', 'public'),
                    'image' => $file['image']->storePublicly('lectures/images', 'public'),
                    'name' => $file['name'],
                    'lecture_id' => $lecture->id
                ];
            }, $request->validated('files'));
            if (!empty($filesPath)) {
                $lecture->attachedFiles()->createMany($filesPath);
            }
        }

        if ($request->has('paragraphs')) {
            $paragraphsData = array_map(function ($paragraph) use ($lecture) {
                $data = [
                    'lecture_id' => $lecture->id,
                ];
                if (isset($paragraph['description']))
                    $data['description'] = $paragraph['description'];
                if (isset($paragraph['image']) && $paragraph['image'] instanceof \Illuminate\Http\UploadedFile) {
                    $originalFileName = $paragraph['image']->getClientOriginalName();

                    // Create a timestamp  
                    $timestamp = now()->timestamp; // or use now()->format('YmdHis') for a formatted string  
        
                    // Create a new file name with timestamp and original file name  
                    $newFileName = $timestamp . '_' . $originalFileName;
                    $data['image'] = $paragraph['image']->storePubliclyAs('lectures/paragraphs/images', $newFileName, 'public');
                }

                return $data;
            }, $request->validated('paragraphs'));

            if (!empty($paragraphsData)) {
                $lecture->paragraphs()->createMany($paragraphsData);
            }
        }
        return LectureResource::make($lecture);
    }
    public function update(Section $section, UpdateLectureRequest $request, $type, Lecture $lecture)
    {
        $data = $request->validated();
        if ($request->has('requirements_image') && $request->requirements_image === null) {
            if (Storage::exists("public/{$lecture->requirements_image}")) {
                Storage::delete("public/{$lecture->requirements_image}");
            }
        }
        if ($request->hasFile('requirements_image')) {
            if (Storage::exists("public/{$lecture->requirements_image}")) {
                Storage::delete("public/{$lecture->requirements_image}");
            }
            $originalFileName = $data['requirements_image']->getClientOriginalName();

            // Create a timestamp  
            $timestamp = now()->timestamp; // or use now()->format('YmdHis') for a formatted string  

            // Create a new file name with timestamp and original file name  
            $newFileName = $timestamp . '_' . $originalFileName;
            $data['requirements_image'] = $data['requirements_image']->storePubliclyAs('lectures/images', $newFileName, 'public');
        }
        $lecture->update($data);
        if ($request->has('links')) {
            $linksData = array_map(function ($link) use ($lecture) {
                return [
                    'name' => $link['name'],
                    'link' => $link['link'],
                    'lecture_id' => $lecture->id
                ];
            }, $request->validated('links'));

            if (!empty($linksData)) {
                $lecture->links()->createMany($linksData);
            }
        }
        if (isset($data['delete_links'])) {
            $linksToDelete = $lecture->links()->whereIn('id', $data['delete_links'])->get();
            foreach ($linksToDelete as $link) {
                $lecture->links()->where('id', $link->id)->delete();
            }
        }
        if (isset($data['update_links'])) {
            foreach ($data['update_links'] as $updatedLink) {
                $link = $lecture->links()->find($updatedLink['id']);
                if ($link) {
                    $link->update(Arr::only($updatedLink, ['name', 'link']));
                }
            }
        }
        if ($request->has('images')) {  
            $imagesPath = array_map(function ($image) use ($lecture) {  
                // Get the original file name  
                $originalFileName = $image->getClientOriginalName();  
                
                // Create a timestamp  
                $timestamp = now()->timestamp; // or use now()->format('YmdHis') for a formatted string  
        
                // Create a new file name with timestamp and original file name  
                $newFileName = $timestamp . '_' . $originalFileName;  
        
                // Store the image with the new file name  
                return [  
                    'image' => $image->storePubliclyAs('lectures/images', $newFileName, 'public'),  
                    'lecture_id' => $lecture->id  
                ];  
            }, $request->file('images'));  
        
            if (!empty($imagesPath)) {  
                $lecture->images()->createMany($imagesPath);  
            }  
        }  
        if (isset($data['delete_images'])) {
            $existingImagesCount = $lecture->images()->count();
            $deleteImagesCount = count($data['delete_images']);

            if ($existingImagesCount <= $deleteImagesCount) {
                throw new \Exception(__('messages.can_not_delete_image'), 422);
            }
            $imagesToDelete = $lecture->images()->whereIn('id', $data['delete_images'])->get();
            foreach ($imagesToDelete as $image) {
                if (Storage::exists("public/$image->image")) {
                    Storage::delete("public/$image->image");
                }
                $lecture->images()->where('id', $image->id)->delete();
            }
        }

        if ($request->has('files')) {
            $filesPath = array_map(function ($file) use ($lecture) {
                return [
                    'file' => $file['file']->storePublicly('lectures/files', 'public'),
                    'image' => $file['image']->storePublicly('lectures/images', 'public'),
                    'name' => $file['name'],
                    'lecture_id' => $lecture->id
                ];
            }, $request->validated('files'));

            if (!empty($filesPath)) {
                $lecture->attachedFiles()->createMany($filesPath);
            }
        }
        if (isset($data['delete_files'])) {
            $filesToDelete = $lecture->attachedFiles()->whereIn('id', $data['delete_files'])->get();
            foreach ($filesToDelete as $file) {
                if (Storage::exists("public/$file->file")) {
                    Storage::delete("public/$file->file");
                }
                if (Storage::exists("public/$file->image")) {
                    Storage::delete("public/$file->image");
                }
                $lecture->attachedFiles()->where('id', $file->id)->delete();
            }
        }
        if (isset($data['update_files'])) {
            foreach ($data['update_files'] as $updatedFile) {
                $file = $lecture->attachedFiles()->find($updatedFile['id']);
                if ($file) {
                    if (isset($updatedFile['file'])) {
                        if (Storage::exists("public/{$file->file}")) {
                            Storage::delete("public/{$file->file}");
                        }
                        $updatedFile['file'] = $updatedFile['file']->storePublicly('lectures/files', 'public');
                    }
                    if (isset($updatedFile['image'])) {
                        if (Storage::exists("public/{$file->image}")) {
                            Storage::delete("public/{$file->image}");
                        }
                        $updatedFile['image'] = $updatedFile['image']->storePublicly('lectures/images', 'public');
                    }
                    $file->update(Arr::only($updatedFile, ['file', 'image', 'name']));
                }
            }
        }

        if ($request->has('paragraphs')) {
            $paragraphsData = array_map(function ($paragraph) use ($lecture) {
                $data = [
                    'description' => $paragraph['description'],
                    'lecture_id' => $lecture->id,
                ];

                if (isset($paragraph['image']) && $paragraph['image'] instanceof \Illuminate\Http\UploadedFile) {
                    $originalFileName = $paragraph['image']->getClientOriginalName();

                    // Create a timestamp  
                    $timestamp = now()->timestamp; // or use now()->format('YmdHis') for a formatted string  
        
                    // Create a new file name with timestamp and original file name  
                    $newFileName = $timestamp . '_' . $originalFileName;
                    $data['image'] = $paragraph['image']->storePubliclyAs('lectures/paragraphs/images', $newFileName, 'public');                }

                return $data;
            }, $request->validated('paragraphs'));

            if (!empty($paragraphsData)) {
                $lecture->paragraphs()->createMany($paragraphsData);
            }
        }

        if (isset($data['delete_paragraphs'])) {
            $existingParagraphsCount = $lecture->paragraphs()->count();
            $deleteParagraphsCount = count($data['delete_paragraphs']);

            if ($existingParagraphsCount <= $deleteParagraphsCount) {
                throw new \Exception(__('messages.can_not_delete_paragraph'), 422);
            }
            $paragraphsToDelete = $lecture->paragraphs()->whereIn('id', $data['delete_paragraphs'])->get();
            foreach ($paragraphsToDelete as $paragraph) {
                if (Storage::exists("public/$paragraph->image")) {
                    Storage::delete("public/$paragraph->image");
                }
                $lecture->paragraphs()->where('id', $paragraph->id)->delete();
            }
        }
        if (isset($data['update_paragraphs'])) {
            foreach ($data['update_paragraphs'] as $updatedParagraph) {
                $paragraph = $lecture->paragraphs()->find($updatedParagraph['id']);
                if ($paragraph) {
                    if (isset($updatedParagraph['image'])) {
                        if (Storage::exists("public/{$paragraph->image}")) {
                            Storage::delete("public/{$paragraph->image}");
                        }
                        // todo check if its file
                        $updatedParagraph['image'] = $updatedParagraph['image']->storePublicly('lectures/paragraphs/images', 'public');
                    }
                    $paragraph->update(Arr::only($updatedParagraph, ['image', 'description']));
                }
            }
        }


        return LectureResource::make($lecture);
    }
    public function destroy(Section $section, $type, $lecture, $force = null)
    {
        if ($force) {
            authorize('force_delete_lecture');

            $lecture = Lecture::onlyTrashed()->findOrFail($lecture);

            $images = $lecture->images;
            foreach ($images as $image) {
                if (Storage::exists("public/{$image->image}")) {
                    Storage::delete("public/{$image->image}");
                }
            }

            $files = $lecture->attachedFiles;
            foreach ($files as $file) {
                if (Storage::exists("public/{$file->file}")) {
                    Storage::delete("public/{$file->file}");
                }
                if (Storage::exists("public/$file->image")) {
                    Storage::delete("public/$file->image");
                }
            }

            $paragraphs = $lecture->paragraphs;
            foreach ($paragraphs as $paragraph) {
                if (Storage::exists("public/{$paragraph->image}")) {
                    Storage::delete("public/{$paragraph->image}");
                }
            }

            $lecture->forceDelete();
        } else {
            authorize(['delete_lecture', 'force_delete_lecture']);

            $lecture = Lecture::findOrFail($lecture);
            $lecture->delete();
        }

        return true;
    }
    public function resotre(Section $section, $type, $lecture)
    {
        $lecture = Lecture::withTrashed()->findOrFail($lecture);

        if ($lecture && $lecture->trashed()) {
            $lecture->restore();
            return true;
        }
        throw new \Exception(__('messages.not_found'), 404);

    }
}
