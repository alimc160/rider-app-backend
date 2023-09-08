<?php

namespace Database\Seeders;

use App\Models\VehicleCategory;
use App\Models\VehicleCompany;
use App\Models\VehicleType;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $company = VehicleCompany::updateOrCreate(
            [
                'title' => 'Honda'
            ],[
                'title' => 'Honda'
        ]);
        $category = VehicleCategory::updateOrCreate(
            [
                'title' => 'Bike'
            ],
            [
                'title' => 'Bike'
            ]
        );
        $bikes = ['CD - 70', 'CD - 100', 'CD - 125', 'CD - 150'];
        foreach ($bikes as $bike) {
            VehicleType::updateOrCreate(
                ['title' => $bike],
                [
                    'title' => $bike,
                    'description' => 'Honda',
                    'vehicle_category_id' => $category->id
                ],
            );
        }
    }
}
