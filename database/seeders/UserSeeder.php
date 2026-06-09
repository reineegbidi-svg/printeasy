<?php

namespace Database\Seeders;

use App\Models\PrinterAvailability;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@printeasy.com'],
            [
                'name' => 'Admin PrintEasy',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '+2250700000001',
                'is_active' => true,
            ]
        );

        $printer = User::updateOrCreate(
            ['email' => 'imprimeur@printeasy.com'],
            [
                'name' => 'Imprimerie Campus',
                'password' => Hash::make('password'),
                'role' => 'printer',
                'phone' => '+2250700000002',
                'address' => 'Université Félix Houphouët-Boigny, Cocody',
                'is_active' => true,
                'is_available' => true,
                'is_approved' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@printeasy.com'],
            [
                'name' => 'Jean Kouassi',
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone' => '+2250700000003',
                'is_active' => true,
            ]
        );

        if (User::where('role', 'user')->count() < 4) {
            User::factory(5)->create(['role' => 'user']);
        }

        if (User::where('role', 'printer')->count() < 2) {
            User::factory(2)->create(['role' => 'printer', 'is_available' => true, 'is_approved' => true]);
        }

        foreach (range(1, 5) as $day) {
            PrinterAvailability::updateOrCreate(
                ['printer_id' => $printer->id, 'day_of_week' => $day],
                [
                    'start_time' => '08:00',
                    'end_time' => '18:00',
                    'is_available' => true,
                ]
            );
        }
    }
}
