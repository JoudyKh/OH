<?php

namespace App\Services\General\Notification;

use App\Constants\Constants;
use App\Constants\Notifications;
use App\Http\Resources\NotificationRecourse;
use App\Models\Notification;
use App\Models\User;
use App\Traits\SearchTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class NotificationService
{
    protected ?User $user;

    public function __construct()
    {
        $this->user = auth('sanctum')->user();
    }


    use SearchTrait;
    public function getAllNotifications(Request $request, $hasRead = null, $countOnly = null, $read = '0', $interviewRequestType = null)
    {
        $notifications = $this->user->notifications();
        if ($hasRead !== null) {
            $notifications->where('has_read', $hasRead);
        }
        if ($countOnly) {
            return $notifications->count();
        }
        $this->applySearchAndSort($notifications, $request, Notification::$searchable);
        if ($interviewRequestType !== null) {
            $notifications->where('state', 1)
                ->where('additional_data->interview_request_type', $interviewRequestType);
        }
        $notifications = $notifications->orderBy('id', 'DESC')->paginate(config('app.pagination_limit'));
        if ($read !== '0') {
            $notifications->each(function ($notification) {
                $notification->update(['has_read' => 1]);
            });
        }
        return NotificationRecourse::collection($notifications);
    }
    public function getNotificationTypeStatistics($hasRead = null)
    {
        $stats = $this->user->notifications();
        if ($hasRead !== null) {
            $stats->where('has_read', $hasRead);
        }
        return $stats->select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->pluck('count', 'type');
    }


    public function readAllNotifications()
    {
        return $this->user->notifications()->update(['has_read' => 1]);
    }



    public function pushAdminsNotifications($notification, $user, $interviewRequestType = null, $interviewId = null, $title = null, $description = null)
    {
        switch ($notification['STATE']) {
            case Notifications::NEW_PROJECT['STATE']:
                $description = __('notifications.new_project_description', []);
                $title = __('notifications.new_project_title', []);
                break;
            case Notifications::NEW_REQUEST['STATE']:
                $description = __('notifications.new_interview_request_description', []);
                $title = __('notifications.new_interview_request_title', []);
                break;
            case Notifications::NEW_GRADUATION_PROJECT['STATE']:
                $description = __('notifications.new_graduation_project_description', []);
                $title = __('notifications.new_graduation_project_title', []);
                break;
            default:
                return;
        }
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', Constants::SUPER_ADMIN_ROLE)
                ->orWhere('name', Constants::ADMIN_ROLE)
                ->orWhere('name', Constants::PROJECT_MANAGER_ROLE);
        })->get();

        foreach ($admins as $admin) {
            pushNotification(
                $title,
                $description,
                $notification['TYPE'],
                $notification['STATE'],
                $admin,
                class_basename($user),
                $user->id,
                false,
                $interviewRequestType,
                $interviewId
            );
        }
    }
}
