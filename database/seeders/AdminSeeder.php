<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin_role = Role::updateOrCreate(
            [
                'name' => 'admin',
            ],[
                'name' => 'admin',
                'guard_name' => 'web'
            ]
        );
        $admin_email = 'admin@admin.com';
       $user = User::updateOrCreate(
           [
               'email' => $admin_email
           ],[
               'name' => 'admin',
               'email' => $admin_email,
               'password' => Hash::make('123456')
           ]
       );
       $user->assignRole($admin_role);
    }
}
