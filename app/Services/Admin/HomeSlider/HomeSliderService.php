<?php

namespace App\Services\Admin\HomeSlider;
use App\Http\Requests\Api\Admin\HomeSlider\CreateHomeSliderRequest;
use App\Http\Requests\Api\Admin\HomeSlider\UpdateHomeSliderRequest;
use App\Models\HomeSlider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeSliderService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function store(CreateHomeSliderRequest $request)
    {
        $data = $request->validated();
        if (!$request->hasFile('image'))
            throw new \Exception(__('messages.file_not_sent'), 422);
        $data['image'] = $data['image']->storePublicly('sliders/files', 'public');
        $slider = HomeSlider::create($data);
        return $slider;
    }
    public function update(UpdateHomeSliderRequest $request, HomeSlider $slider)
    {
        $data = $request->validated();
        if($request->hasFile('image')){
            if (Storage::exists("public/$slider->image")) {
                Storage::delete("public/$slider->image");
            }
            $data['image'] = $data['image']->storePublicly('sliders/files', 'public');
        }
        $slider->update($data);
        return $slider;
    }
    public function destroy(HomeSlider $slider)
    {
        if (Storage::exists("public/$slider->image")) {
            Storage::delete("public/$slider->image");
        }
        $slider->delete();
        return true;
    }
}
