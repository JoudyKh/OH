<?php

namespace App\Services\General\Library;
use App\Http\Resources\LibraryResource;
use App\Models\Library;
use App\Models\Section;
use App\Traits\SearchTrait;
use Illuminate\Http\Request;

class LibraryService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    use SearchTrait;
    public function index(Section $section, Request $request)
    {
        $libraries = $section->libraries()->with(['librarySec', 'images']);
        $libraries = $libraries->orderBy($request->trash ? 'deleted_at' : 'sort_order');
        $this->applySearchAndSort($libraries, $request, Library::$searchable);
        $maxSortOrder = $libraries->max('sort_order');
        $libraries = $libraries->paginate(config('app.pagination_limit'));
        return [  
            'libraries' => LibraryResource::collection($libraries),  
            'max_sort_order' => $maxSortOrder,  
        ];
    }
    public function show(Section $section, Library $library)
    {
        $libraries = $section->libraries()->orderBy('sort_order')->get();
    
        $currentIndex = $libraries->search(function ($item) use ($library) {
            return $item->id === $library->id;
        });
    
        $previousLibraryId = null;
        $nextLibraryId = null;
    
        if ($currentIndex > 0) {
            $previousLibraryId = $libraries[$currentIndex - 1]->id;
        }
    
        if ($currentIndex < $libraries->count() - 1) {
            $nextLibraryId = $libraries[$currentIndex + 1]->id;
        }
    
        return [
            'library' => LibraryResource::make($library),
            'previous_library_id' => $previousLibraryId,
            'next_library_id' => $nextLibraryId,
        ];
    }
}
