<?php

namespace App\Services\App\GraduationProject;
use App\Constants\Constants;
use App\Constants\Notifications;
use App\Http\Requests\Api\App\GraduationProject\CreateGraduationProjectRequest;
use App\Http\Resources\GraduationProjectResource;
use App\Models\GraduationProjectRequest;
use App\Models\User;
use App\Services\General\Notification\NotificationService;
use App\Mail\AdminNotificationMail;
use Illuminate\Support\Facades\Mail;
class GraduationProjectService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected NotificationService $notificationService)
    {
        //
    }
    public function store(CreateGraduationProjectRequest $request)
    {
        $data = $request->validated();
        $project = GraduationProjectRequest::create($data);
        $this->notificationService->pushAdminsNotifications(Notifications::NEW_GRADUATION_PROJECT, $project);
        
        $admins = User::where('is_notifiable', 1)
        ->whereHas('roles', function($query) {
            $query->whereIn('name', [Constants::ADMIN_ROLE, Constants::PROJECT_MANAGER_ROLE, Constants::SUPER_ADMIN_ROLE]);
        })->get();
        foreach ($admins as $admin) {
            Mail::to($admin->email)->queue(new AdminNotificationMail($project));
        }
        return GraduationProjectResource::make($project);
    }
}
