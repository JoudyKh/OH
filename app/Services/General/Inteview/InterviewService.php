<?php

namespace App\Services\General\Inteview;
use App\Http\Resources\InterviewResource;
use App\Models\Interview;
use App\Traits\SearchTrait;
use Illuminate\Http\Request;

class InterviewService
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
        $interviews = Interview::with('images')->orderBy($request->trash ? 'deleted_at' : 'sort_order');
        if ($request->type)
            $interviews = $interviews->where('type', $request->type);
        $this->applySearchAndSort($interviews, $request, Interview::$searchable);
        $maxSortOrder = $interviews->max('sort_order');
        $interviews = $interviews->paginate(config('app.pagination_limit'));
        return [  
            'interviews' => InterviewResource::collection($interviews),  
            'max_sort_order' => $maxSortOrder,  
        ];
    }
    public function show(Interview $interview)
    {
        $interviews = Interview::orderBy('sort_order')->get();

        $currentIndex = $interviews->search(function ($item) use ($interview) {
            return $item->id === $interview->id;
        });

        $previousInterviewId = null;
        $nextInterviewId = null;

        if ($currentIndex > 0) {
            $previousInterviewId = $interviews[$currentIndex - 1]->id;
        }

        if ($currentIndex < $interviews->count() - 1) {
            $nextInterviewId = $interviews[$currentIndex + 1]->id;
        }

        return [
            'interview' => InterviewResource::make($interview->load('images')),
            'previous_interview_id' => $previousInterviewId,
            'next_interview_id' => $nextInterviewId,
        ];
    }
}
