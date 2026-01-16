<?php

namespace App\Services\General\Classification;
use App\Models\Classification;
use App\Traits\SearchTrait;
use Illuminate\Http\Request;

class ClassificationService
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
        $classifications = Classification::query();
        $this->applySearchAndSort($classifications, $request, Classification::$searchable);

        $classifications = $classifications->get();

        return $classifications;
    }
}
