<?php

namespace Database\Seeders;

use App\Constants\Constants;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $models = [
            'AdView',
            'Classification',
            'GraduationProjectRequest',
            'HomeSlider',
            'Info',
            'Interview',
            'InterviewImage',
            'InterviewRequest',
            'Lecture',
            'LectureAttachedFile',
            'LectureImage',
            'LectureLink',
            'LectureParagraph',
            'Library',
            'LibraryFile',
            'Notification',
            'Section',
            'StudentProject',
            'StudentProjectFile',
            'User',
            'UserImage',
            'University',
        ];
        $softDeleteModels = [
            'GraduationProjectRequest',
            'InterviewRequest',
            'StudentProject',
            'User',
            'Interview',
            'Lecture',
            'Library',
            'Section',
        ];
        $permissions = [
            'create',
            'edit',
            'delete',
            'view',
        ];
        foreach ($models as $model) {
            $model_snake_case = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $model));
            
            foreach ($permissions as $permission) {
                Permission::create(['name' => "{$permission}_{$model_snake_case}"]);
            }

            if (in_array($model, $softDeleteModels)) {
                Permission::create(['name' => "force_delete_{$model_snake_case}"]);
                Permission::create(['name' => "restore_{$model_snake_case}"]);
            }
        }

        $superAdminRole = Role::where('name', Constants::SUPER_ADMIN_ROLE)->first();
        if ($superAdminRole) {
            $superAdminRole->permissions()->attach(Permission::pluck('id')->toArray());
        }
        
        $adminRole = Role::where('name', Constants::ADMIN_ROLE)->first();
        if ($adminRole) {
            $adminRole->permissions()->attach(Permission::pluck('id')->toArray());
        }

        $contentManagerRole = Role::where('name', Constants::CONTENT_MANAGER_ROLE)->first();
        if ($contentManagerRole) {
            $contentManagerPermissions = [
                'view_section',
                'create_section',
                'edit_section',
                'delete_section',
                'view_library',
                'create_library',
                'edit_library',
                'delete_library',
                'view_library_file',
                'create_library_file',
                'edit_library_file',
                'delete_library_file',
                'view_interview_request',
                'delete_interview_request',
                'view_interview',
                'create_interview',
                'edit_interview',
                'delete_interview',
                'view_interview_image',
                'create_interview_image',
                'edit_interview_image',
                'delete_interview_image',
                'view_classification',
                'create_classification',
                'edit_classification',
                'delete_classification',
                'view_notification',
                'view_university',
                'create_university',
                'edit_university',
                'delete_university',
            ];
        
            $permissions = Permission::whereIn('name', $contentManagerPermissions)->get();
        
            $contentManagerRole->permissions()->attach($permissions);
        }

        $projectManagerRole = Role::where('name', Constants::PROJECT_MANAGER_ROLE)->first();
        if ($projectManagerRole) {
            $projectManagerPermissions = [
                'view_student_project',
                'edit_student_project',
                'delete_student_project',
                'view_graduation_project_request',
                'edit_graduation_project_request',
                'delete_graduation_project_request',
                'view_notification',
                'view_university',
            ];
        
            $permissions = Permission::whereIn('name', $projectManagerPermissions)->get();
        
            $projectManagerRole->permissions()->attach($permissions);
        }
    }
}
