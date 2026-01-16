<?php

namespace App\Services\Admin\Inteview;
use App\Http\Requests\Api\Admin\Interview\CreateInterviewRequest;
use App\Http\Requests\Api\Admin\Interview\UpdateInterviewRequest;
use App\Http\Resources\InterviewResource;
use App\Models\Interview;
use Illuminate\Support\Facades\Storage;

class InterviewService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function store(CreateInterviewRequest $request)
    {
        $data = $request->validated();
        $interview = Interview::create($data);
        if ($request->has('images')) {
            $imagesPath = [];

            foreach ($request->file('images') as $image) {
                $uploadedPath = $image->storePublicly('interview/images', 'public');

                if ($uploadedPath) {
                    $imagesPath[] = [
                        'image' => $uploadedPath,
                        'institution_id' => $interview->id
                    ];
                }
            }

            if (!empty($imagesPath)) {
                $interview->images()->createMany($imagesPath);
            }
        }

        return InterviewResource::make($interview->load('images'));
    }
    public function update(UpdateInterviewRequest $request, Interview $interview)
    {
        $data = $request->validated();
        if (isset($data['delete_images'])) {
            $imagesToDelete = $interview->images()->whereIn('id', $data['delete_images'])->get();
            foreach ($imagesToDelete as $image) {
                Storage::delete("public/$image->image");
                $interview->images()->where('id', $image->id)->delete();
            }
        }
        if ($request->has('images')) {
            $imagesPath = [];

            foreach ($request->file('images') as $image) {
                $uploadedPath = $image->storePublicly('interview/images', 'public');

                if ($uploadedPath) {
                    $imagesPath[] = [
                        'image' => $uploadedPath,
                        'institution_id' => $interview->id
                    ];
                }
            }

            if (!empty($imagesPath)) {
                $interview->images()->createMany($imagesPath);
            }
        }
        $interview->update($data);
        return InterviewResource::make($interview->load('images'));
    }
    public function destroy($interview, $force = null)
    {
        if ($force) {
            authorize('force_delete_interview');

            $interview = Interview::onlyTrashed()->findOrFail($interview);

            foreach ($interview->images as $image) {
                if (Storage::disk('public')->exists($image->image)) {
                    Storage::disk('public')->delete($image->image);
                }
            }
            $interview->forceDelete();
        } else {
            authorize(['delete_interview', 'force_delete_interview']);

            $interview = Interview::where('id', $interview)->first();
            $interview->delete();
        }
        return true;
    }
    public function restore($interview)
    {
        $interview = Interview::withTrashed()->find($interview);
        if ($interview && $interview->trashed()) {
            $interview->restore();
            return true;
        }
        throw new \Exception(__('messages.not_found'), 404);
    }
}
