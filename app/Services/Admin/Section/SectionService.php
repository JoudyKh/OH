<?php

namespace App\Services\Admin\Section;
use App\Http\Requests\Api\Admin\Section\StoreSectionRequest;
use App\Http\Requests\Api\Admin\Section\UpdateSectionRequest;
use App\Http\Resources\SectionResource;
use App\Models\Section;
use Illuminate\Support\Facades\Storage;

class SectionService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function store(StoreSectionRequest $request, Section $parentSection = null, $type)
    {
        $data = $request->validated();
        if ($parentSection) {
            if ($parentSection->type !== 'super_section' && $parentSection->type !== 'super_library_section')
                throw new \Exception(__('messages.invalid_parent_id'));
            if ($type === 'sub_section' || $type === 'sub_library_section') {
                $data['parent_id'] = $parentSection->id;
            }
        }
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->storePublicly('sections/images', 'public');
        }
        $section = Section::create($data);
        return SectionResource::make($section);
    }
    public function update(UpdateSectionRequest $request, Section $section)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->storePublicly('sections/images', 'public');
            if (Storage::exists("public/$section->image")) {
                Storage::delete("public/$section->image");
            }
        }
        $section->update($data);
        return SectionResource::make($section);
    }
    public function delete($id, $force = null)
    {
        if ($force) {
            authorize('force_delete_section');

            $section = Section::onlyTrashed()->findOrFail($id);
            if (Storage::exists("public/$section->image"))
                Storage::delete("public/$section->image");
            $section->forceDelete();
        } else {
            authorize(['delete_section', 'force_delete_section']);

            $section = Section::with('subLibrariesSec')->where('id', $id)->first();
            // return $section;
            // return $section->subLibrariesSec;
            // $section->subLibrariesSec()->delete();
            $section->delete();
        }
        return true;
    }
    public function restore($id)
    {
        $section = Section::withTrashed()->find($id);

        if ($section && $section->trashed()) {
            $section->restore();
            return true;
        }
        throw new \Exception(__('messages.not_found'), 404);

    }
}
