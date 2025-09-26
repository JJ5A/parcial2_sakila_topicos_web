<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Store;
use Carbon\Carbon;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stores = [
            [
                'manager_staff_id' => 1,
                'address_id' => 1,
                'last_update' => Carbon::now(),
            ],
            [
                'manager_staff_id' => 3,
                'address_id' => 3,
                'last_update' => Carbon::now(),
            ],
        ];

        foreach ($stores as $storeData) {
            Store::create($storeData);
        }
    }
}
