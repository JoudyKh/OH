<?php

namespace App\Services\App\StudentProject;
use App\Constants\Constants;
use App\Constants\Notifications;
use App\Http\Requests\Api\App\StudentProject\CreateStuProRequest;
use App\Http\Resources\StudentProjectResource;
use App\Models\StudentProject;
use App\Models\User;
use App\Services\General\Notification\NotificationService;
use App\Traits\SearchTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\AdminNotificationMail;
use Illuminate\Support\Facades\Mail;
class StudentProjectService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected NotificationService $notificationService)
    {
        //
    }
    use SearchTrait;
    public function index(Request $request)
    {
        $student = Auth::guard('student')->user();
        $projects = $student->projects()->with(['files', 'university']);
        $this->applySearchAndSort($projects, $request, StudentProject::$searchable);
        $projects = $projects->paginate(config('app.pagination_limit'));
        return StudentProjectResource::collection($projects);
    }
    public function store(CreateStuProRequest $request)
    {
        $data = $request->validated();
        $student = Auth::guard('student')->user();
        $data['student_id'] = $student->id;
        $project = StudentProject::create($data);
        if ($request->has('files')) {
            $filesPath = [];
    
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    // return $file->getClientOriginalName();
                    $uploadedPath = $file->storePublicly('project/files', 'public');
                    
                    if ($uploadedPath) {
                        $filesPath[] = [
                            'file' => $uploadedPath,  
                            'student_project_id' => $project->id,
                            'name' => $file->getClientOriginalName(),
                        ];
                    }
                }
            } 
            else {
                $filesPath[] = [
                    'file' => $request->input('files'),  
                    'student_project_id' => $project->id,
                ];
            }
    
            if (!empty($filesPath)) {
                $project->files()->createMany($filesPath);
            }
        }
        $this->notificationService->pushAdminsNotifications(Notifications::NEW_PROJECT, $project);
        $admins = User::where('is_notifiable', 1)
        ->whereHas('roles', function($query) {
            $query->whereIn('name', [Constants::ADMIN_ROLE, Constants::PROJECT_MANAGER_ROLE, Constants::SUPER_ADMIN_ROLE]);
        })->get();
    
        foreach ($admins as $admin) {
            Mail::to($admin->email)->queue(new AdminNotificationMail($project)); 
        }
        return StudentProjectResource::make($project->load('files'));
    }
}
