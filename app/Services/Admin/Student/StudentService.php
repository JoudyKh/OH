<?php

namespace App\Services\Admin\Student;
use App\Constants\Constants;
use App\Http\Requests\Api\Admin\Student\CreateStudentRequest;
use App\Http\Requests\Api\Admin\Student\UpdateStudentRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\SearchTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentService
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
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', Constants::SUPER_ADMIN_ROLE);
            $query->where('name', Constants::ADMIN_ROLE);
        })->whereHas('roles', function ($query) {
            $query->where('name', Constants::STUDENT_ROLE);
        });

        $this->applySearchAndSort($users, $request, User::$searchable);

        $users->with('university')->orderByDesc($request->trash ? 'deleted_at' : 'created_at');

        $users = $users->paginate(config('app.pagination_limit'));

        return UserResource::collection($users);

    }
    public function show(User $student)
    {
        if (!$student->roles()->where('name', Constants::STUDENT_ROLE)->exists()) {
            throw new \Exception(__('messages.forbidden_user'), 403);
        }
        return UserResource::make($student);
    }
    public function store(CreateStudentRequest $request)
    {
        $data = $request->validated();
        $student = User::create($data);
        $data['password'] = Hash::make($data['password']);
        $student->assignRole(Constants::STUDENT_ROLE);

        return UserResource::make($student->load('university'));
    }
    public function update(UpdateStudentRequest $request, User $student)
    {
        if (!$student->roles()->where('name', Constants::STUDENT_ROLE)->exists()) {
            throw new \Exception(__('messages.forbidden_user'), 403);
        }
        $data = $request->validated();
        if (isset($data['password']))
            $data['password'] = Hash::make($data['password']);
        $student->update($data);
        $student = User::find($student->id);
        return UserResource::make($student->load('university'));
    }
    public function destroy($studentId, $force = null)
    {
        $student = User::findOrFail($studentId);

        if (!$student->roles()->where('name', Constants::STUDENT_ROLE)->exists()) {
            throw new \Exception(__('messages.forbidden_user'), 403);
        }else{
            if ($force) {
                $student = User::onlyTrashed()->findOrFail($studentId);
                $student->forceDelete();
            } else {
                $student->projects()->delete();
                $student->delete(); 
            }
            return true;
        }

    }
    public function restore($student)
    {
        $student = User::withTrashed()->find($student);
        if (!$student->roles()->where('name', Constants::STUDENT_ROLE)->exists()) {
            throw new \Exception(__('messages.forbidden_user'), 403);
        }
        if ($student && $student->trashed()) {
            $student->restore();
            $student->projects()->restore();
            return true;
        }
        throw new \Exception(__('messages.not_found'), 404);
    }
}
