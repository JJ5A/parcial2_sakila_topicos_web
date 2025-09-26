<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            ['name' => 'English', 'last_update' => now()],
            ['name' => 'Spanish', 'last_update' => now()],
            ['name' => 'French', 'last_update' => now()],
            ['name' => 'German', 'last_update' => now()],
        ];

        // Insertar idiomas
        DB::table('language')->insert($languages);
    }
}
