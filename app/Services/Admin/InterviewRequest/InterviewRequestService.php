<?php

namespace App\Services\Admin\InterviewRequest;
use App\Http\Resources\InterviewRequestResource;
use App\Models\Interview;
use App\Models\InterviewRequest;
use App\Traits\SearchTrait;
use Illuminate\Http\Request;

class InterviewRequestService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    use SearchTrait;
    public function index(Request $request, Interview $interview)
    {

        $interviewRequests = $interview->requests()->with(['interview', 'university']);
        $this->applySearchAndSort($interviewRequests, $request, InterviewRequest::$searchable);
        $interviewRequests = $interviewRequests->orderByDesc('created_at')->paginate(config('app.pagination_limit'));
        return InterviewRequestResource::collection($interviewRequests);
    }
    public function destroy(Interview $interview, $request, $force = null)
    {
        if ($force) {
            authorize( 'force_delete_interview_request');

            $request = InterviewRequest::onlyTrashed()->findOrFail($request);
            $request->forceDelete();
        } else {
            authorize(['delete_interview_request', 'force_delete_interview_request']);

            $request = InterviewRequest::where('id', $request)->first();
            $request->delete();
        }
        return true;
    }
    public function restore(Interview $interview, $request)
    {
        $request = InterviewRequest::withTrashed()->find($request);
        if ($request && $request->trashed()) {
            $request->restore();
            return true;
        }
        throw new \Exception(__('messages.not_found'), 404);
    }
}
