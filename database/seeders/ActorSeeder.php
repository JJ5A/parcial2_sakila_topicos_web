<?php

namespace Database\Seeders;

use App\Models\Actor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear algunos actores famosos como datos de prueba
        $actors = [
            ['first_name' => 'Leonardo', 'last_name' => 'DiCaprio'],
            ['first_name' => 'Meryl', 'last_name' => 'Streep'],
            ['first_name' => 'Robert', 'last_name' => 'De Niro'],
            ['first_name' => 'Scarlett', 'last_name' => 'Johansson'],
            ['first_name' => 'Morgan', 'last_name' => 'Freeman'],
            ['first_name' => 'Natalie', 'last_name' => 'Portman'],
            ['first_name' => 'Brad', 'last_name' => 'Pitt'],
            ['first_name' => 'Angelina', 'last_name' => 'Jolie'],
            ['first_name' => 'Tom', 'last_name' => 'Hanks'],
            ['first_name' => 'Jennifer', 'last_name' => 'Lawrence'],
        ];

        foreach ($actors as $actor) {
            Actor::create($actor);
        }

        // Crear 40 actores adicionales usando el factory
        Actor::factory()->count(40)->create();
    }
}
