<?php

namespace Database\Seeders;

use App\Models\TagGroup;
use Illuminate\Database\Seeder;

class TagGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = array_map(fn ($item) => $item['value'], config('group-tags'));

        foreach ($types as $type)
        {
            TagGroup::factory()->create([
                'type' => $type,
                'locale' => 'it'
            ]);
        }

        foreach ($types as $type)
        {
            TagGroup::factory()->create([
                'type' => $type,
                'locale' => 'gb'
            ]);
        }
        
    }
}
