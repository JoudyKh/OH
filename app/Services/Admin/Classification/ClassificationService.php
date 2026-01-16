<?php

namespace App\Services\Admin\Classification;
use App\Http\Requests\Api\Admin\Classification\CreateClassificationRequest;
use App\Http\Requests\Api\Admin\Classification\UpdateClassificationRequest;
use App\Models\Classification;

class ClassificationService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function store(CreateClassificationRequest $request)
    {
        $data = $request->validated();
        return Classification::create($data);
    }
    public function update(UpdateClassificationRequest $request, Classification $Classification)
    {
        $data = $request->validated();
        $Classification->update($data);
        return $Classification;
    }
    public function destroy($Classification)
    {
        $Classification = Classification::where('id', $Classification)->first();
        $Classification->delete();

        return true;
    }
}
