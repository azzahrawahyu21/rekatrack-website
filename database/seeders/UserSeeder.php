<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\user;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        // $adminRole = Role::where('name', 'Admin')->first();
        // $driverRole = Role::where('name', 'Driver')->first();

        // Menambahkan Super Admin untuk IT
        User::create([
            'nip' => '1',
            'role_id' => $superAdminRole->id,
            'name' => 'Super Admin IT',
            'email' => 'superadmin.it@gmail.com',
            'password' => Hash::make('password'),
            'phone_number' => '081234567890',
        ]);

        // Menambahkan Admin dan Driver untuk Logistik
        // User::create([
        //     'nip' => '2',
        //     'role_id' => $adminRole->id,
        //     'name' => 'Admin Logistik',
        //     'email' => 'admin.logistik@example.com',
        //     'password' => Hash::make('password'),
        //     'phone_number' => '081234567891',
        // ]);

        // User::create([
        //     'nip' => '3',
        //     'role_id' => $driverRole->id,
        //     'name' => 'Driver Logistik',
        //     'email' => 'driver.logistik@example.com',
        //     'password' => Hash::make('password'),
        //     'phone_number' => '081234567892',
        // ]);
    }
}
