<?php

namespace Database\Seeders;

use App\Models\VehicleType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CitiesSeeder::class,
            VehicleSeeder::class,
            RoleSeeder::class,
            AdminSeeder::class
        ]);
    }
}
