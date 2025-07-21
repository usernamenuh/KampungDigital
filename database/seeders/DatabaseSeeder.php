<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();


      //  User::factory()->create([
       //     'name' => 'admin',
         //   'email' => 'admin@gmail.com',
           // 'password'=> bcrypt('123456789'),
            // 'role' => 'admin',
                // 'status' => 'active',
       // ]);
      DB::unprepared(file_get_contents(database_path('seeders/wilayah_indonesia.sql')));

    }
}
