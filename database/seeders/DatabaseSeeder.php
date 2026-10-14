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

        Warehouse::create(['warehouse' => 'Warehouse 1']);
        Warehouse::create(['warehouse' => 'Warehouse 2']);

        User::create(['name' => 'Admin', 'email' => 'admin@email.com', 'password' => Hash::make('password'), 'role_id' => 1]);

    }
}