<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@jobyou.test')],
            [
                'name' => env('ADMIN_NAME', 'JobYou Admin'),
                'password' => env('ADMIN_PASSWORD', 'password'),
                'status' => true,
            ],
        );
    }
}
