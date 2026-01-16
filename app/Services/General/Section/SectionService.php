<?php

namespace App\Services\General\Section;
use App\Http\Resources\SectionResource;
use App\Models\Section;
use App\Traits\SearchTrait;
use Illuminate\Http\Request;

class SectionService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    use SearchTrait;
    public function getAll(Request $request, $parentSection = null, $type)
    {
        $sections = Section::query();

        $parentSection && $parentSection != 'all' ?
            $sections = $parentSection->subSections()->with('superSection') :

            $sections = $type === 'super_library_section' ?
            $sections->where('type', 'sub_library_section') :
            $sections->where('type', $type);

        if ($request->sub_type)
            $sections = $sections->where('sub_type', $request->sub_type);
        $this->applySearchAndSort($sections, $request, Section::$searchable);
        $maxSortOrder = $sections->max('sort_order');
        $sections = $sections->orderBy($request->trash ? 'deleted_at' : 'sort_order')->paginate(config('app.pagination_limit'));
        return [
            'sections' => SectionResource::collection($sections),
            'max_sort_order' => $maxSortOrder,
        ];
    }
    public function show(Section $section)
    {
        if ($section->type === 'super_library_section') {
            $section = $section->with('subLibrariesSec');
        }
        return SectionResource::make($section);
    }
}
