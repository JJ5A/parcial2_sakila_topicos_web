<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Address;
use Carbon\Carbon;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $addresses = [
            [
                'address' => 'Calle Principal #123',
                'address2' => 'Apto 4B',
                'district' => 'Centro',
                'city_id' => 1,
                'postal_code' => '12345',
                'phone' => '+1-234-567-8901',
                'last_update' => Carbon::now(),
            ],
            [
                'address' => 'Avenida Libertad #456',
                'address2' => null,
                'district' => 'Norte',
                'city_id' => 1,
                'postal_code' => '12346',
                'phone' => '+1-234-567-8902',
                'last_update' => Carbon::now(),
            ],
            [
                'address' => 'Calle de la Paz #789',
                'address2' => 'Casa 12',
                'district' => 'Sur',
                'city_id' => 2,
                'postal_code' => '12347',
                'phone' => '+1-234-567-8903',
                'last_update' => Carbon::now(),
            ],
            [
                'address' => 'Boulevard Central #321',
                'address2' => null,
                'district' => 'Este',
                'city_id' => 1,
                'postal_code' => '12348',
                'phone' => '+1-234-567-8904',
                'last_update' => Carbon::now(),
            ],
            [
                'address' => 'Paseo de la Reforma #654',
                'address2' => 'Oficina 301',
                'district' => 'Oeste',
                'city_id' => 2,
                'postal_code' => '12349',
                'phone' => '+1-234-567-8905',
                'last_update' => Carbon::now(),
            ],
            [
                'address' => 'Calle del Sol #987',
                'address2' => null,
                'district' => 'Centro',
                'city_id' => 1,
                'postal_code' => '12350',
                'phone' => '+1-234-567-8906',
                'last_update' => Carbon::now(),
            ],
            [
                'address' => 'Avenida de los Insurgentes #147',
                'address2' => 'Depto 5A',
                'district' => 'Norte',
                'city_id' => 2,
                'postal_code' => '12351',
                'phone' => '+1-234-567-8907',
                'last_update' => Carbon::now(),
            ],
            [
                'address' => 'Calle Juárez #258',
                'address2' => null,
                'district' => 'Sur',
                'city_id' => 1,
                'postal_code' => '12352',
                'phone' => '+1-234-567-8908',
                'last_update' => Carbon::now(),
            ],
        ];

        foreach ($addresses as $addressData) {
            Address::create($addressData);
        }
    }
}
