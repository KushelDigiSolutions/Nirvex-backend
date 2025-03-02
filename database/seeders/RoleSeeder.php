<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role; // Import the Role class

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create the 'customers' role if it doesn't already exist
        if (!Role::where('name', 'customers')->exists()) {
            Role::create(['name' => 'customers', 'guard_name' => 'web']);
        }
    }
}
