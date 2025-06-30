<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class RtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $rwIds = DB::table('rws')->pluck('id')->toArray();
        $data = [];
        foreach ($rwIds as $rwId) {
            for ($i = 1; $i <= 3; $i++) {
                $data[] = [
                    'rw_id' => $rwId,
                    'nama_rt' => 'RT 00' . $i,
                    'alamat' => $faker->address,
                    'ketua_rt' => $faker->optional()->name,
                    'no_telpon' => $faker->optional()->numberBetween(1000000000, 2147483647),
                    'jumlah_kk' => $faker->numberBetween(10, 100),
                    'saldo' => $faker->randomFloat(2, 0, 10000000),
                    'status' => $faker->randomElement(['aktif', 'tidak_aktif']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        DB::table('rts')->insert($data);
    }
}
