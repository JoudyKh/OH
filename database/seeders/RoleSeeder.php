<?php

namespace Database\Seeders;

use App\Constants\Constants;
use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => Constants::SUPER_ADMIN_ROLE,
                'guard_name' => 'api',
            ],
            [
                'name' => Constants::ADMIN_ROLE,
                'guard_name' => 'api',
            ],
            [
                'name' => Constants::CONTENT_MANAGER_ROLE,
                'guard_name' => 'api',
            ],
            [
                'name' => Constants::PROJECT_MANAGER_ROLE,
                'guard_name' => 'api',
            ],
            [
                'name' => Constants::STUDENT_ROLE,
                'guard_name' => 'api',
            ],
        ];
        Role::insert($roles);

        $admin = User::create([
            'name' => 'admin',
            'email' => 'joudyalkhatib38@gmail.com',
            'phone_number' => '+963967213544',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('12345678'),
            'is_active' => '1',
        ]);
        $admin->assignRole(Constants::SUPER_ADMIN_ROLE);

    }
}
