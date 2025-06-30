<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class DesaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $data = [];
        for ($i = 0; $i < 20; $i++) {
            $data[] = [
                'province_code' => $faker->numberBetween(10, 99),
                'regency_code' => $faker->numberBetween(10, 99),
                'district_code' => $faker->numberBetween(10, 99),
                'village_code' => $faker->numberBetween(1000, 9999),
                'alamat' => $faker->address,
                'kode_pos' => $faker->numberBetween(10000, 99999),
                'no_telpon' => $faker->optional()->numberBetween(1000000000, 2147483647),
                'gmail' => $faker->unique()->safeEmail,
                'saldo' => $faker->randomFloat(2, 0, 10000000),
                'status' => $faker->randomElement(['aktif', 'tidak_aktif']),
                'foto' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('desas')->insert($data);
    }
}
