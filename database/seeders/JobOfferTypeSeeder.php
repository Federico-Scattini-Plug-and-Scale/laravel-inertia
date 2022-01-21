<?php

namespace Database\Seeders;

use App\Models\JobOfferType;
use Illuminate\Database\Seeder;

class JobOfferTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        JobOfferType::factory()->create([
            'locale' => 'it',
        ]);
        JobOfferType::factory()->create([
            'locale' => 'it',
        ]);
        JobOfferType::factory()->create([
            'locale' => 'it',
        ]);
        JobOfferType::factory()->create([
            'locale' => 'gb',
        ]);
        JobOfferType::factory()->create([
            'locale' => 'gb',
        ]);
        JobOfferType::factory()->create([
            'locale' => 'gb',
        ]);
    }
}
