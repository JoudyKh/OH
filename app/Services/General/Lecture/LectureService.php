<?php

namespace App\Services\General\Lecture;
use App\Constants\Constants;
use App\Http\Resources\LectureResource;
use App\Models\Lecture;
use App\Models\LectureAttachedFile;
use App\Models\Section;
use App\Traits\SearchTrait;
use Illuminate\Http\Request;

class LectureService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    use SearchTrait;
    public function index(Section $section, Request $request, $type)
{
    // Fetch lectures based on the section and type
    $lectures = $section->lectures()->where('lectures.type', $type);

    // Order lectures based on the request (trash or sort_order)
    $lectures = $lectures->orderBy($request->trash ? 'deleted_at' : 'sort_order');

    // Apply search and sort filters
    $this->applySearchAndSort($lectures, $request, Lecture::$searchable);

    // Get the maximum sort order for the lectures
    $maxSortOrder = $lectures->max('sort_order');

    // Paginate the lectures
    $paginatedLectures = $lectures->paginate(config('app.pagination_limit'));

    // Transform the paginated lectures
    $transformedLectures = $paginatedLectures->map(function ($lecture) {
        return [
            'id' => $lecture->id,
            'name' => $lecture->name,
            'first_image' => $lecture->images->first() ? $lecture->images->first()->image : null,
            'sort_order' => $lecture->sort_order,
        ];
    });

    // Return the paginated and transformed data
    return [
        'lectures' => [
            'current_page' => $paginatedLectures->currentPage(),
            'data' => $transformedLectures,
            'first_page_url' => $paginatedLectures->url(1),
            'from' => $paginatedLectures->firstItem(),
            'last_page' => $paginatedLectures->lastPage(),
            'last_page_url' => $paginatedLectures->url($paginatedLectures->lastPage()),
            'links' => $paginatedLectures->links(),
            'next_page_url' => $paginatedLectures->nextPageUrl(),
            'path' => $paginatedLectures->path(),
            'per_page' => $paginatedLectures->perPage(),
            'prev_page_url' => $paginatedLectures->previousPageUrl(),
            'to' => $paginatedLectures->lastItem(),
            'total' => $paginatedLectures->total(),
        ],
        'max_sort_order' => $maxSortOrder,
    ];
}
    public function search(Request $request)
    {
        $query = Lecture::query();

        $this->applySearchAndSort($query, $request, Lecture::$searchable);
        return $query->with(['images', 'subSection.superSection'])->get();
    }
    public function downloadFile(Section $section, $type, Lecture $lecture,LectureAttachedFile $file)
    {
        $filePath = storage_path('app/public/' . $file->file);
        return response()->download($filePath);
    }
    public function show(Section $section, $type, Lecture $lecture)
    {
        $lectures = $section->lectures()->orderBy('sort_order')->get();

        $currentIndex = $lectures->search(function ($item) use ($lecture) {
            return $item->id === $lecture->id;
        });

        $previousLectureId = null;
        $nextLectureId = null;

        if ($currentIndex > 0) {
            $previousLectureId = $lectures[$currentIndex - 1]->id;
        }

        if ($currentIndex < $lectures->count() - 1) {
            $nextLectureId = $lectures[$currentIndex + 1]->id;
        }
        if ($type === 'project')
            $lecture->load('classification');
        return [
            'lecture' => LectureResource::make($lecture),
            'previous_lecture_id' => $previousLectureId,
            'next_lecture_id' => $nextLectureId,
        ];
    }
}
