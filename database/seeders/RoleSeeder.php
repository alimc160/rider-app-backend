<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = ['admin', 'rider'];
        Role::updateOrCreate(
            [
                'name' => 'admin',
            ], [
                'name' => 'admin',
                'guard_name' => 'web'
            ]
        );
        Role::updateOrCreate(
            [
                'name' => 'rider',
            ], [
                'name' => 'rider',
                'guard_name' => 'sanctum'
            ]
        );
    }
}
