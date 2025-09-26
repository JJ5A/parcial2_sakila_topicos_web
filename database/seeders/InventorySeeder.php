<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear inventario para las primeras 5 películas
        $inventoryData = [];
        
        for ($filmId = 1; $filmId <= 5; $filmId++) {
            // Crear 3-5 copias por película
            for ($copy = 1; $copy <= rand(3, 5); $copy++) {
                $inventoryData[] = [
                    'film_id' => $filmId,
                    'store_id' => rand(1, 2),
                    'available' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('inventory')->insert($inventoryData);
        $this->command->info('Se crearon ' . count($inventoryData) . ' elementos de inventario.');
    }
}
