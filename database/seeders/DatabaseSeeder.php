<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Warehouse;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'supervisor']);
        Role::create(['name' => 'encoder']);
        Role::create(['name' => 'accounting']);

        Warehouse::create(['warehouse' => 'Calle']);
        Warehouse::create(['warehouse' => 'Sterling']);

        User::create(['name' => 'Admin', 'email' => 'admin@email.com', 'password' => Hash::make('inmed@superadmin'), 'role_id' => 1]);
        User::create(['name' => 'Supervisor', 'email' => 'supervisor@email.com', 'password' => Hash::make('inmed@supervisor'), 'role_id' => 2]);
        User::create(['name' => 'Encoder', 'email' => 'encoder@email.com', 'password' => Hash::make('inmed@encoder'), 'role_id' => 3]);
        User::create(['name' => 'Accounting', 'email' => 'accounting@email.com', 'password' => Hash::make('inmed@accounting'), 'role_id' => 4]);
    }
}
