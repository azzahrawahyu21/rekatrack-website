<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            DivisionSeeder::class,      
            RoleSeeder::class,           
            UserSeeder::class,           
            // TrackSeeder::class,          
            // LocationSeeder::class,       
            // UnitsSeeder::class,
            // TravelDocumentSeeder::class, 
            // ItemsSeeder::class,          
            // TrackingSystemSeeder::class, 
        ]);
    }
}
