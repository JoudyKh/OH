<?php

namespace App\Services\General\HomeSlider;
use App\Models\HomeSlider;
use App\Traits\SearchTrait;
use Illuminate\Http\Request;

class HomeSliderService
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
        $sliders = HomeSlider::orderByDesc($request->trash ? 'deleted_at' : 'created_at');
        $this->applySearchAndSort($sliders, $request, HomeSlider::$searchable);
        $sliders = $sliders->paginate(config('app.pagination_limit'));
        return $sliders;
    }
    public function indexCities()
    {
        return HomeSlider::distinct()->pluck('city');
    }
    public function show(HomeSlider $slider)
    {
        return $slider;
    }
}
