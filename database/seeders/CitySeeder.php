<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            ['city' => 'Ciudad de Mexico', 'country_id' => 1, 'last_update' => Carbon::now()],
            ['city' => 'Guadalajara', 'country_id' => 1, 'last_update' => Carbon::now()],
            ['city' => 'New York', 'country_id' => 2, 'last_update' => Carbon::now()],
            ['city' => 'Los Angeles', 'country_id' => 2, 'last_update' => Carbon::now()],
            ['city' => 'Madrid', 'country_id' => 3, 'last_update' => Carbon::now()],
        ];

        DB::table('city')->insert($cities);
    }
}
