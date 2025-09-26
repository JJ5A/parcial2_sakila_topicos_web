<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            ['country' => 'Mexico', 'last_update' => Carbon::now()],
            ['country' => 'United States', 'last_update' => Carbon::now()],
            ['country' => 'Spain', 'last_update' => Carbon::now()],
            ['country' => 'Argentina', 'last_update' => Carbon::now()],
            ['country' => 'Colombia', 'last_update' => Carbon::now()],
        ];

        DB::table('country')->insert($countries);
    }
}
