<?php

namespace App\Services\Admin\University;

use App\Http\Requests\Api\Admin\University\CreateUniversityRequest;
use App\Http\Requests\Api\Admin\University\UpdateUniversityRequest;
use App\Http\Resources\UniversityResource;
use App\Models\University;

class UniversityService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function store(CreateUniversityRequest $request)
    {
        $data = $request->validated();
        $university = University::Create($data);
        return UniversityResource::make($university);
    }
    public function update(UpdateUniversityRequest $request, University $university)
    {
        $data = $request->validated();
        $university->update($data);
        return UniversityResource::make($university);
    }
    public function destroy(University $university)
    {
        $university->delete();
        return true;
    }
}
