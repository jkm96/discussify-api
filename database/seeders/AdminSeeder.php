<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Utils\Helpers\AuthHelpers;
use Carbon\Carbon;
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
            $profileUrl = AuthHelpers::createUserAvatarFromName("jkmdroid", true);
            Admin::create([
                'username' => 'jkm96.dev',
                'email' => 'jkm96.dev@discussify.io',
                'password' => Hash::make('jkm@!2Pac'),
                'is_active' => true,
                'profile_url' => $profileUrl,
                'is_email_verified' => 1,
                'email_verified_at' => Carbon::now()
            ]);
        } else {
            $this->command->info('Admin user already exists, skipping creation.');
        }
    }
}
