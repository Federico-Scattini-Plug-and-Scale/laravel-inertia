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
        DB::table('users')->truncate(); //for cleaning earlier data to avoid duplicate entries
        DB::table('users')->insert([
          'name' => 'the admin user',
          'email' => 'admin@gmail.com',
          'role' => 'admin',
          'password' => Hash::make('password'),
        ]);
        DB::table('users')->insert([
          'name' => 'the developer user',
          'email' => 'developer@gmail.com',
          'role' => 'developer',
          'password' => Hash::make('password'),
        ]);
        DB::table('users')->insert([
            'name' => 'the company user',
            'email' => 'company@gmail.com',
            'role' => 'company',
            'password' => Hash::make('password'),
        ]);
      }
}
