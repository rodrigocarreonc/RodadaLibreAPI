<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use App\Models\User;

class RolesUserAndAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleUser = Role::create(['name' => 'user']);
        $roleAdmin = Role::create(['name' => 'admin']);

        $admin = User::create([
            'name' => env('SEED_ADMIN_NAME'),
            'email' => env('SEED_ADMIN_EMAIL'),
            'password' => env('SEED_ADMIN_PASSWORD'),
        ]);

        $admin->assignRole($roleAdmin);

        $user = User::create([
            "name" => env('SEED_USER_NAME'),
            "email" => env('SEED_USER_EMAIL'),
            "password" => env('SEED_USER_PASSWORD'),
        ]);

        $user->assignRole($roleUser);
    }
}
