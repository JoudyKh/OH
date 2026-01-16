<?php

namespace App\Services\App\Home;
use App\Http\Resources\InterviewResource;
use App\Http\Resources\LibraryResource;
use App\Http\Resources\SectionResource;
use App\Models\HomeSlider;
use App\Models\Interview;
use App\Models\Library;
use App\Models\Section;

class HomeService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function index()
    {
        $sections = Section::where('type', 'super_section')->orderBy('sort_order')->take(8)->get();
        $interviews = Interview::where('is_special', 1)->with('images')->orderBy('sort_order')->take(8)->get();
        $libraries = Library::where('is_special', 1)->orderBy('sort_order')->take(8)->get();
        $sliders = HomeSlider::orderByDesc('created_at');
        $subLibrarySections = Section::where('type', 'sub_library_section')->where('is_special', 1)->orderBy('sort_order')->take(8)->get();
        return [
            'sections' => SectionResource::collection($sections),
            'interviews' => InterviewResource::collection($interviews),
            'libraries' => LibraryResource::collection($libraries),
            'sliders' => $sliders,
            'sub_library_sections' => $subLibrarySections,
        ];
    }
}
