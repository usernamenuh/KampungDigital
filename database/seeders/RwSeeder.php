<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class RwSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $desaIds = DB::table('desas')->pluck('id')->toArray();
        $data = [];
        foreach ($desaIds as $desaId) {
            for ($i = 1; $i <= 3; $i++) {
                $data[] = [
                    'desa_id' => $desaId,
                    'nama_rw' => 'RW 00' . $i,
                    'alamat' => $faker->address,
                    'no_telpon' => $faker->optional()->numberBetween(1000000000, 2147483647),
                    'saldo' => $faker->randomFloat(2, 0, 10000000),
                    'status' => $faker->randomElement(['aktif', 'tidak_aktif']),
                    'ketua_rw' => $faker->optional()->name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        DB::table('rws')->insert($data);
    }
}
