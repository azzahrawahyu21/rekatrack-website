<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $logistikDivision = Division::where('name', 'LOGISTIK')->first();
        $itDivision = Division::where('name', 'IT')->first();

        Role::create(['division_id' => $itDivision->id, 'name' => 'Super Admin']);
        
        Role::create(['division_id' => $logistikDivision->id, 'name' => 'Admin']);
        Role::create(['division_id' => $logistikDivision->id, 'name' => 'Driver']);
    }
}
