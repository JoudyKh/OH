<?php

namespace App\Services\General\University;

use App\Http\Resources\UniversityResource;
use App\Models\University;
use App\Traits\SearchTrait;
use Illuminate\Http\Request;

class UniversityService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    use SearchTrait;
    public function index(Request $request)
    {
        $universities = University::query();
        $this->applySearchAndSort($universities, $request, University::$searchable);
        $universities = $request->paginate === '0' ?
            $universities->get() :
            $universities->paginate(config('app.pagination_limit'));
        return UniversityResource::collection($universities);
    }
}
