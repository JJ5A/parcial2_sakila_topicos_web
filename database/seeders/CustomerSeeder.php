<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'first_name' => 'María',
                'last_name' => 'González',
                'email' => 'maria.gonzalez@email.com',
                'phone' => '555-0101',
                'address' => 'Calle Principal 123',
                'city' => 'Ciudad de México',
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Juan',
                'last_name' => 'Pérez',
                'email' => 'juan.perez@email.com',
                'phone' => '555-0102',
                'address' => 'Avenida Libertad 456',
                'city' => 'Guadalajara',
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Ana',
                'last_name' => 'Martín',
                'email' => 'ana.martin@email.com',
                'phone' => '555-0103',
                'address' => 'Boulevard Central 789',
                'city' => 'Monterrey',
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Carlos',
                'last_name' => 'Rodríguez',
                'email' => 'carlos.rodriguez@email.com',
                'phone' => '555-0104',
                'address' => 'Calle del Sol 321',
                'city' => 'Puebla',
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Lucía',
                'last_name' => 'Fernández',
                'email' => 'lucia.fernandez@email.com',
                'phone' => '555-0105',
                'address' => 'Paseo de la Reforma 654',
                'city' => 'Tijuana',
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('customers')->insert($customers);
    }
}
