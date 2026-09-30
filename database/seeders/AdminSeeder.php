<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use InvalidArgumentException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('admin.email');
        $password = config('admin.password');

        if (! is_string($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Set a valid ADMIN_EMAIL before seeding the administrator.');
        }

        if (! is_string($password) || strlen($password) < 12 || $password === 'password') {
            throw new InvalidArgumentException('Set an ADMIN_PASSWORD of at least 12 characters before seeding the administrator.');
        }

        Admin::updateOrCreate(
            ['email' => $email],
            [
                'name' => config('admin.name', 'JobYou Admin'),
                'password' => $password,
                'status' => true,
            ],
        );
    }
}
