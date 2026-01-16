<?php

namespace App\Services\Admin\Admins;

use App\Constants\Constants;
use App\Http\Requests\Api\Admin\Admins\CreateAdminRequest;
use App\Http\Requests\Api\Admin\Admins\UpdateAdminRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\SearchTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminsService
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
        $validatedData = $request->validate([
            'role' => ['required', Rule::in([Constants::PROJECT_MANAGER_ROLE, Constants::CONTENT_MANAGER_ROLE, Constants::ADMIN_ROLE])]
        ]);
        $role = $validatedData['role'];
        $admins = User::with('roles')->whereHas('roles', function ($query) use ($role) {
            $query->where('name', $role);
        });
        $this->applySearchAndSort($admins, $request, User::$searchable);
        if ($request->trash && Auth::user()?->hasRole([Constants::ADMIN_ROLE, Constants::SUPER_ADMIN_ROLE])) {
            $admins->onlyTrashed();
        }
        $admins = $admins->orderByDesc($request->trash ? 'deleted_at' : 'created_at')->paginate(config('app.pagination_limit'));
        return UserResource::collection($admins);
    }
    public function show(User $admin)
    {
        return UserResource::make($admin);
    }
    public function store(CreateAdminRequest $request)
    {
        $data = $request->validated();
        $admin = User::create($data);
        $admin->assignRole($data['role']);
        return UserResource::make($admin);
    }
    public function update(UpdateAdminRequest $request, User $admin)
    {
        $data = $request->validated();
        $admin->update($data);
        if (isset($data['role'])) {
            $previousRole = $admin->getRoleNames()->first();
            if ($data['role'] === 'project_manager')
                $newRole = Constants::PROJECT_MANAGER_ROLE;
            elseif ($data['role'] === 'content_manager')
                $newRole = Constants::CONTENT_MANAGER_ROLE;
            else
                $newRole = Constants::ADMIN_ROLE;

            if ($previousRole !== $newRole) {
                $admin->removeRole($previousRole);
                $admin->assignRole($newRole);
            }
        }
        return UserResource::make($admin);
    }
    public function destroy($adminId, $force = null)
    {
        $admin = User::withTrashed()->findOrFail($adminId);
        if ($admin->roles->contains('name', Constants::SUPER_ADMIN_ROLE)) {
            throw new \Exception(__('messages.forbidden'));
        }
        if ($force) {
            authorize('force_delete_user');
            $admin = User::onlyTrashed()->findOrFail($adminId);
            $admin->forceDelete();
        } else {
            authorize(['delete_user', 'force_delete_user']);
            $admin->delete();
        }
        return true;
    }
    public function restore($admin)
    {
        $admin = User::withTrashed()->find($admin);
        if ($admin && $admin->trashed()) {
            $admin->restore();
            return true;
        }
        throw new \Exception(__('messages.not_found'), 404);
    }
}
