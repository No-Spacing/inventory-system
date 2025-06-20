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

        Warehouse::create(['warehouse' => 'Calli']);
        Warehouse::create(['warehouse' => 'Sterling']);

        User::create(['name' => 'Admin', 'email' => 'admin@email.com', 'password' => Hash::make('admin123'), 'role_id' => 1]);
        User::create(['name' => 'Supervisor', 'email' => 'supervisor@email.com', 'password' => Hash::make('supervisor123'), 'role_id' => 2]);
        User::create(['name' => 'Encoder', 'email' => 'encoder@email.com', 'password' => Hash::make('encoder123'), 'role_id' => 3]);
    }
}
