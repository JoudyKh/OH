<?php

use App\Constants\Constants;
use App\Models\AdView;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Torann\GeoIP\Facades\GeoIP;

if (!function_exists('error')) {
    function error(string $message = null, $errors = null, $code = 401)
    {
        return response()->json([
            'message' => $message,
            'errors' => $errors ?? [$message],
            'code' => (($code < 400 || $code > 503) ? 500 : $code),
        ], (($code < 400 || $code > 503) ? 500 : $code));
    }
}
if (!function_exists('success')) {
    function success($data = null, int $code = Response::HTTP_OK, $additionalData = [])
    {
        return response()->json(
            array_merge([
                'data' => $data ?? ['success' => true],
                'code' => $code
            ], $additionalData),
            $code
        );
    }
}
if (!function_exists('throwError')) {
    function throwError($message, $errors = null, int $code = Response::HTTP_UNPROCESSABLE_ENTITY)
    {
        throw new HttpResponseException(response()->json(
            [
                'message' => $message,
                'errors' => $errors ?? [$message],
            ],
            $code
        ));
    }
}


if (!function_exists('paginate')) {
    function paginate(&$data, $paginationLimit = null)
    {
        $paginationLimit = $paginationLimit ?? config('app.pagination_limit');
        $page = LengthAwarePaginator::resolveCurrentPage();
        $paginatedStudents = collect($data)->forPage($page, $paginationLimit);

        // Create a LengthAwarePaginator-like structure
        $paginator = new LengthAwarePaginator(
            $paginatedStudents,
            count($data),
            $paginationLimit,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        // Convert the paginator to an array with numerically indexed data
        $data = $paginator->toArray();
        $data['data'] = collect($data['data'])->values()->all();

        return $data;
    }
}
if (!function_exists('diffForHumans')) {
    function diffForHumans($time)
    {
        return Carbon::parse($time)->diffForHumans(Carbon::now(), [
            'long' => true,
            'parts' => 2,
            'join' => true,
        ]);
    }
}
if (!function_exists('pushNotification')) {
    function pushNotification($title, $description, $type, $state, $user, $modelType, $modelId, $checkDuplicated = false, $interviewRequestType = null, $interviewId = null)
    {
        $data = [
            'title' => $title,
            'description' => $description,
            'type' => $type,
            'state' => $state,
            'model_id' => $modelId,
            'model_type' => $modelType,
        ];

        $additionalData = [];

        if ($interviewRequestType) {
            $additionalData['interview_request_type'] = $interviewRequestType;
        }
        if ($interviewId) {
            $additionalData['interview_id'] = $interviewId;
        }

        if (!empty($additionalData)) {
            $data['additional_data'] = json_encode($additionalData);
        }
        if ($checkDuplicated) {
            $filteredData = array_diff_key($data, array_flip(['title', 'description']));
            if ($user === null) {
                User::whereHas('roles', function ($q) use ($filteredData, $data) {
                    $q->where('name', Constants::ADMIN_ROLE);
                })->get()->map(function ($user) use ($filteredData, $data) {
                    $user->notifications()->firstOrCreate($filteredData, $data);
                });
            } else
                $user->notifications()->firstOrCreate($filteredData, $data);
        } else {
            if ($user === null) {
                User::whereHas('roles', function ($q) {
                    $q->where('name', Constants::SUPER_ADMIN_ROLE);
                })->get()->map(function ($user) use ($data) {
                    $user->notifications()->create($data);
                });
            } else
                $user->notifications()->create($data);
        }
    }

}
if (!function_exists('visitorsCount')) {
    function visitorsCount(Request $request, $model = null)
    {
        $user = auth()->user();
        $ipAddress = $request->ip();
        $fingerPrint = $request->header('finger_print');
        $location = GeoIP::getLocation($ipAddress)->toArray();
        $userId = $user->id ?? null;
        $viewType = $request->input('view');

        DB::transaction(function () use ($ipAddress, $fingerPrint, $location, $userId, $viewType, $model) {
            $checkView = AdView::where(function ($q) use ($ipAddress, $userId) {
                $q->where('ip', $ipAddress);
                if ($userId) {
                    $q->orWhere('user_id', $userId);
                }
            })
                ->where('finger_print', $fingerPrint)
                ->where('view', $viewType)
                ->lockForUpdate()
                ->exists();

            if (!$checkView) {
                AdView::create([
                    'ip' => $ipAddress,
                    'view' => $viewType,
                    'user_id' => $userId,
                    'country' => $location['country'],
                    'geo_info' => json_encode($location),
                    'finger_print' => $fingerPrint,
                    'model_type' => $model ? get_class($model) : null,
                    'model_id' => $model ? $model->id : null,
                ]);
            }
        });
        return AdView::where('view', $viewType)
            ->when($model, function ($query) use ($model) {
                $query->where('model_id', $model->id);
            })
            ->count('ip');

    }

}
if (!function_exists('authorize')) {
    function authorize(string|array $permission, $allowGuestForNonAdminRoute = false)
    {
        if ($allowGuestForNonAdminRoute and !request()->is('*admin*'))
            return;

        if (!auth('sanctum')->check()) {
            throw new AuthenticationException();
        }

        $user = User::with(['roles.permissions', 'permissions'])->find(auth('sanctum')->id());

        if (is_string($permission)) {
            $permission = [$permission];
        }
    
        $hasDirectPermission = $user->permissions->pluck('name')->intersect($permission)->isNotEmpty();
    
        $hasRolePermission = $user->roles->load('permissions')
                             ->pluck('permissions') 
                             ->flatten()
                             ->pluck('name') 
                             ->intersect($permission) 
                             ->isNotEmpty();
    
        if (!$hasDirectPermission && !$hasRolePermission) {
            throw new AuthorizationException();
        }
    }
}

