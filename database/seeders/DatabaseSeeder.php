<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Ejecutar los seeders en orden correcto
        $this->call([
            // Datos base del sistema
            LanguageSeeder::class,  // Idiomas (requeridos por películas)
            FilmSeeder::class,      // Películas
            ActorSeeder::class,     // Actores
            
            // Datos para el sistema de rentas
            CustomerSeeder::class,  // Clientes
            StaffSeeder::class,     // Empleados
            InventorySeeder::class, // Inventario (requiere películas)
        ]);
    }
}
