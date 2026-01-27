<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure the admin role exists
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['description' => 'Administrator with full access']
        );

        // Create Admin Account (Required for login check)
        $account = \App\Models\UserAccount::firstOrCreate(
            ['id' => 1],
            [
                'user_account' => 'Admin Account',
                'active_flag' => 1,
                'company' => 'Dakia Admin',
                'email' => 'admin@example.com'
            ]
        );

        // Create admin user if it does not exist
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'), // Change after first login
                'active_flag' => 1,
                'user_account_id' => $account->id
            ]
        );

        // Attach role
        $admin->roles()->syncWithoutDetaching([$adminRole->id]);
    }
}
?>
