<?php

namespace Database\Seeders;

use App\Models\JobOffer;
use Illuminate\Database\Seeder;

class JobOffersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        JobOffer::factory()->count(1000)->create();
    }
}
