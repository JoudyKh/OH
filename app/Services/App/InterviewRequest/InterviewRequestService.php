<?php

namespace App\Services\App\InterviewRequest;
use App\Constants\Constants;
use App\Constants\Notifications;
use App\Http\Requests\Api\App\InterviewRequest\CreateInterviewRequest;
use App\Http\Resources\InterviewRequestResource;
use App\Models\Interview;
use App\Models\InterviewRequest;
use App\Models\User;
use App\Services\General\Notification\NotificationService;
use Illuminate\Support\Facades\Auth;
use App\Mail\AdminNotificationMail;
use Illuminate\Support\Facades\Mail;
class InterviewRequestService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected NotificationService $notificationService)
    {
        //
    }
    public function store(Interview $interview, CreateInterviewRequest $request, $type)
    {
        $data = $request->validated();
        if (
            ($interview->requests_type === 'cartoon_certificate' && $type === 'electronic_certificate')
            || ($interview->requests_type === 'electronic_certificate' && $type === 'cartoon_certificate')
            || ($interview->requests_type == null && $type != 'participation')
        )
            throw new \Exception(__('messages.invalid_request_type'), 422);
        $student = Auth::guard('student')->user();
        $data['type'] = $type;
        $data['interview_id'] = $interview->id;
        $interviewRequest = InterviewRequest::create($data);
        //  $student->requests()->create($data);
        if ($type === 'electronic_certificate')
            $arType = 'شهادة الكترونية';
        elseif ($type === 'cartoon_certificate')
            $arType = 'شهادة كرتونية';
        else
            $arType = 'مشاركة';
        $this->notificationService->pushAdminsNotifications(Notifications::NEW_REQUEST, $interviewRequest, $arType, $interview->id);
        $admins = User::where('is_notifiable', 1)
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', [Constants::ADMIN_ROLE, Constants::CONTENT_MANAGER_ROLE, Constants::SUPER_ADMIN_ROLE]);
            })->get();

        foreach ($admins as $admin) {
            Mail::to($admin->email)->queue(new AdminNotificationMail($interviewRequest));
        }
        return InterviewRequestResource::make($interviewRequest);
    }
}
