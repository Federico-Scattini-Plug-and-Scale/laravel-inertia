<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('users')->insert([
          'email' => 'admin@gmail.com',
          'role' => 'admin',
          'password' => Hash::make('password'),
          'email_verified_at' => now()
        ]);
        DB::table('users')->insert([
          'email' => 'applicant@gmail.com',
          'role' => 'applicant',
          'password' => Hash::make('password'),
          'email_verified_at' => now()
        ]);
        DB::table('users')->insert([
            'email' => 'company@gmail.com',
            'role' => 'company',
            'password' => Hash::make('password'),
            'email_verified_at' => now()
        ]);
      }
}
