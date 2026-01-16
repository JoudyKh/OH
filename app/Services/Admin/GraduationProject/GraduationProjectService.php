<?php

namespace App\Services\Admin\GraduationProject;
use App\Http\Resources\GraduationProjectResource;
use App\Models\GraduationProjectRequest;
use App\Traits\SearchTrait;
use Illuminate\Http\Request;

class GraduationProjectService
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
        $projects = GraduationProjectRequest::with('university')->orderByDesc('created_at');
        $this->applySearchAndSort($projects, $request, GraduationProjectRequest::$searchable);
        $projects = $projects->paginate(config('app.pagination_limit'));
        return GraduationProjectResource::collection($projects);
    }
    public function show(GraduationProjectRequest $project)
    {
        return GraduationProjectResource::make($project);
    }
    public function destroy($project, $force = null)
    {
        if ($force) {
            authorize( 'force_delete_graduation_project_request');

            $project = GraduationProjectRequest::onlyTrashed()->findOrFail($project);
            $project->forceDelete();
        } else {
            authorize(['delete_graduation_project_request', 'force_delete_graduation_project_request']);

            $project = GraduationProjectRequest::where('id', $project)->first();
            $project->delete();
        }
        return true;
    }
    public function restore($project)
    {
        $project = GraduationProjectRequest::withTrashed()->find($project);
        if ($project && $project->trashed()) {
            $project->restore();
            return true;
        }
        throw new \Exception(__('messages.not_found'), 404);
    }
}
