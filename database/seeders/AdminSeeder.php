<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Utils\Helpers\AuthHelpers;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::truncate();
        if (Admin::count() === 0) {
            $profileUrl = AuthHelpers::createUserAvatarFromName("petdiariesd", true);
            Admin::create([
                'username' => 'jkmdroid',
                'email' => 'jkmdroid@petdiaries.io',
                'password' => Hash::make('jkm@2pac'),
                'is_active' => true,
                'profile_url' => $profileUrl
            ]);
            $this->command->info('Admin user created successfully!');
        } else {
            $this->command->info('Admin user already exists, skipping creation.');
        }
    }
}
