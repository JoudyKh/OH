<?php

namespace App\Services\General\LibraryFile;
use App\Http\Resources\LibraryFileResource;
use App\Models\LibraryFile;
use App\Models\Section;
use App\Traits\SearchTrait;
use Illuminate\Http\Request;

class LibraryFileService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    use SearchTrait;
    public function index(Request $request,Section $section)
    {
        $files = $section->LibraryFiles()->orderBy('sort_order');
        $this->applySearchAndSort($files, $request, LibraryFile::$searchable);
        $maxSortOrder = $files->max('sort_order');
        $files = $files->paginate(20);
        // return LibraryFileResource::collection($files);
        return [  
            'files' => LibraryFileResource::collection($files),  
            'max_sort_order' => $maxSortOrder,  
        ];
    }
    public function show(Section $section, LibraryFile $file)
    {
        return LibraryFileResource::make($file);
    }
    public function downloadFile(LibraryFile $file)
    {
        $filePath = storage_path('app/public/' . $file->file);
        return response()->download($filePath);
    }
}
