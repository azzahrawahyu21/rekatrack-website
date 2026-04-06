<?php

namespace Database\Seeders;

use App\Models\Track;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TrackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $driver = User::where('nip', '3')->first();

        for ($i = 1; $i <= 7; $i++) {
            Track::create([
                'driver_id' => $driver->id,
                'time_stamp' => now()->addDays($i),
                'status' => 'active',
            ]);
        }
    }
}
