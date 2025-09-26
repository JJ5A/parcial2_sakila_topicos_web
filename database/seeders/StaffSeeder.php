<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staffMembers = [
            [
                'first_name' => 'Roberto',
                'last_name' => 'Manager',
                'address_id' => 1,
                'email' => 'roberto.manager@sakila.com',
                'store_id' => 1,
                'username' => 'roberto_mgr',
                'active' => 1,
                'last_update' => now(),
            ],
            [
                'first_name' => 'Sofia',
                'last_name' => 'Empleada',
                'address_id' => 2,
                'email' => 'sofia.empleada@sakila.com',
                'store_id' => 1,
                'username' => 'sofia_emp',
                'active' => 1,
                'last_update' => now(),
            ],
            [
                'first_name' => 'Diego',
                'last_name' => 'Supervisor',
                'address_id' => 3,
                'email' => 'diego.supervisor@sakila.com',
                'store_id' => 2,
                'username' => 'diego_sup',
                'active' => 1,
                'last_update' => now(),
            ],
        ];

        DB::table('staff')->insert($staffMembers);
    }
}
