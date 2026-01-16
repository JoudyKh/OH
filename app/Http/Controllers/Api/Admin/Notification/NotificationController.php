<?php

namespace App\Http\Controllers\Api\Admin\Notification;

use App\Constants\Constants;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\General\Notification\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(protected NotificationService $notificationService)
    {}
      /**
     * @OA\Get(
     *     path="/notifications/{read}",
     *     tags={"App" , "App - Notification"},
     *     summary="Get all notification",
     *     @OA\Parameter(
     *         name="hasRead",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             enum={"0","1"},
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="countOnly",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example=""
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="read",
     *         in="path",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example=""
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="interviewRequestType",
     *         in="query",
     *         required=false,
     *         @OA\Schema(enum={"شهادة الكترونية","شهادة كرتونية","مشاركة"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *      security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     )
     * )
     * @OA\Get(
     *     path="/admin/notifications/{read}",
     *     tags={"Admin" , "Admin - Notification"},
     *     summary="Get all notification ",
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="hasRead",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             enum={"0","1"},
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="countOnly",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example=""
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="read",
     *         in="path",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example=""
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="interviewRequestType",
     *         in="query",
     *         required=false,
     *         @OA\Schema(enum={"شهادة الكترونية","شهادة كرتونية","مشاركة"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     )
     * )
     */
    public function getAllNotifications(Request $request, $read = null,$hasRead = null, $countOnly = null)
    {
        authorize('view_notification'); 
        $unreadCount = $this->notificationService->getAllNotifications($request, 0, true);
        $notifications = $this->notificationService->getAllNotifications($request, $hasRead, $countOnly, $read,$request->interviewRequestType);

        return success($notifications, 200, ['unread_notifications_count' => $unreadCount]);
    }
}
