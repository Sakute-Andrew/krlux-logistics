<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Коментуємо це, щоб не викликати помилку fake()
        // \App\Models\User::factory(10)->create();

        // Створюємо конкретного адміна
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@admin.com'], // шукаємо по email
            [
                'name' => 'Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }
}
