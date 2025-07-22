<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Carbon\Carbon;

class PendudukSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $kkIds = DB::table('kks')->pluck('id');

        if ($kkIds->isEmpty()) {
            $this->command->warn('Tidak ada data KK ditemukan, seeder Penduduk dilewati.');
            return;
        }

        foreach (range(1, 50) as $i) {
            $jenis_kelamin = $faker->randomElement(['L', 'P']);
            $tanggal_lahir = $faker->dateTimeBetween('-70 years', '-1 years');
            $tanggal_lahir_fmt = $tanggal_lahir->format('Y-m-d');
            $day = (int)date('d', strtotime($tanggal_lahir_fmt));
            if ($jenis_kelamin == 'P') {
                $day += 40;
            }
            $nik = '327312' . str_pad($day, 2, '0', STR_PAD_LEFT) .
                   date('m', strtotime($tanggal_lahir_fmt)) .
                   date('y', strtotime($tanggal_lahir_fmt)) .
                   str_pad($i, 4, '0', STR_PAD_LEFT);

            DB::table('penduduks')->insert([
                'nik' => $nik,
                'kk_id' => $kkIds->random(),
                'user_id' => null,
                'nama_lengkap' => $faker->name($jenis_kelamin == 'L' ? 'male' : 'female'),
                'jenis_kelamin' => $jenis_kelamin,
                'tempat_lahir' => $faker->city,
                'tanggal_lahir' => $tanggal_lahir_fmt,
                'agama' => $faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']),
                'pendidikan' => $faker->randomElement([
                    'Tidak/Belum Sekolah', 'Belum Tamat SD/Sederajat', 'Tamat SD/Sederajat',
                    'SLTP/Sederajat', 'SLTA/Sederajat', 'Diploma I/II',
                    'Akademi/Diploma III/S.Muda', 'Diploma IV/Strata I', 'Strata II', 'Strata III'
                ]),
                'pekerjaan' => $faker->jobTitle,
                'status_perkawinan' => $faker->randomElement(['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']),
                'hubungan_keluarga' => $faker->randomElement([
                    'Kepala Keluarga', 'Istri', 'Anak', 'Menantu', 'Cucu',
                    'Orangtua', 'Mertua', 'Famili Lain', 'Pembantu', 'Lainnya'
                ]),
                'kewarganegaraan' => 'WNI',
                'no_paspor' => null,
                'tanggal_expired_paspor' => null,
                'nama_ayah' => $faker->name('male'),
                'nama_ibu' => $faker->name('female'),
                'status_penduduk' => $faker->randomElement(['Tetap', 'Tidak Tetap', 'Pendatang']),
                'tanggal_pindah' => null,
                'alamat_sebelumnya' => $faker->optional()->address,
                'status' => $faker->randomElement(['aktif', 'tidak_aktif']),
                'tanggal_meninggal' => null,
                'keterangan' => $faker->optional()->sentence,
                'foto' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
