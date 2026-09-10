<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesUserAndAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleUser = Role::firstOrCreate(['name' => 'user']);
        $roleModerator = Role::firstOrCreate(['name' => 'moderator']);
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);

        $admin = User::create([
            'first_name' => env('SEED_ADMIN_FIRST_NAME'),
            'last_name' => env('SEED_ADMIN_LAST_NAME'),
            'email' => env('SEED_ADMIN_EMAIL'),
            'password' => env('SEED_ADMIN_PASSWORD'),
        ]);

        $admin->assignRole($roleAdmin);

        $moderator = User::create([
            'first_name' => env('SEED_MODERATOR_FIRST_NAME'),
            'last_name' => env('SEED_MODERATOR_LAST_NAME'),
            'email' => env('SEED_MODERATOR_EMAIL'),
            'password' => env('SEED_MODERATOR_PASSWORD'),
        ]);

        $moderator->assignRole($roleModerator);

        $user = User::create([
            'first_name' => env('SEED_USER_FIRST_NAME'),
            'last_name' => env('SEED_USER_LAST_NAME'),
            'email' => env('SEED_USER_EMAIL'),
            'password' => env('SEED_USER_PASSWORD'),
        ]);

        $user->assignRole($roleUser);
    }
}
