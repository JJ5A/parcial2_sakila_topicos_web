<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FilmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Películas famosas como datos de prueba
        $films = [
            [
                'title' => 'The Shawshank Redemption',
                'description' => 'Two imprisoned men bond over a number of years, finding solace and eventual redemption through acts of common decency.',
                'release_year' => 1994,
                'language_id' => 1,
                'rental_duration' => 6,
                'rental_rate' => 4.99,
                'length' => 142,
                'replacement_cost' => 19.99,
                'rating' => 'R',
                'special_features' => 'Trailers,Commentaries,Behind the Scenes',
                'last_update' => now()
            ],
            [
                'title' => 'The Godfather',
                'description' => 'The aging patriarch of an organized crime dynasty transfers control of his clandestine empire to his reluctant son.',
                'release_year' => 1972,
                'language_id' => 1,
                'rental_duration' => 7,
                'rental_rate' => 5.99,
                'length' => 175,
                'replacement_cost' => 24.99,
                'rating' => 'R',
                'special_features' => 'Trailers,Commentaries,Deleted Scenes',
                'last_update' => now()
            ],
            [
                'title' => 'The Dark Knight',
                'description' => 'When the menace known as the Joker wreaks havoc and chaos on the people of Gotham, Batman must accept one of the greatest psychological and physical tests.',
                'release_year' => 2008,
                'language_id' => 1,
                'rental_duration' => 5,
                'rental_rate' => 3.99,
                'length' => 152,
                'replacement_cost' => 18.99,
                'rating' => 'PG-13',
                'special_features' => 'Trailers,Behind the Scenes',
                'last_update' => now()
            ],
            [
                'title' => 'Pulp Fiction',
                'description' => 'The lives of two mob hitmen, a boxer, a gangster and his wife intertwine in four tales of violence and redemption.',
                'release_year' => 1994,
                'language_id' => 1,
                'rental_duration' => 6,
                'rental_rate' => 4.99,
                'length' => 154,
                'replacement_cost' => 22.99,
                'rating' => 'R',
                'special_features' => 'Trailers,Commentaries,Deleted Scenes,Behind the Scenes',
                'last_update' => now()
            ],
            [
                'title' => 'Inception',
                'description' => 'A thief who steals corporate secrets through the use of dream-sharing technology is given the inverse task of planting an idea.',
                'release_year' => 2010,
                'language_id' => 1,
                'rental_duration' => 5,
                'rental_rate' => 4.99,
                'length' => 148,
                'replacement_cost' => 21.99,
                'rating' => 'PG-13',
                'special_features' => 'Trailers,Commentaries,Behind the Scenes',
                'last_update' => now()
            ],
        ];

        DB::table('film')->insert($films);
    }
}
