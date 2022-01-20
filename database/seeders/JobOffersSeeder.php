<?php

namespace Database\Seeders;

use App\Models\JobOffer;
use App\Models\Tag;
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
        $offers = JobOffer::factory()->count(1000)->create();
        $groupTag = Tag::query()
                        ->select('id', 'tag_group_id')
                        ->with(['tagGroup' => function ($query) {
                            $query->select('id', 'type');
                        }])
                        ->get()
                        ->groupBy('tagGroup.type')
                        ->toArray();
        
        foreach ($offers as $offer)
        {
            $relatedTags = [];
            foreach ($groupTag as $group)
            {
                $randKeys = array_rand($group, rand(1, 3));
               
                if (is_array($randKeys))
                {
                    foreach ($randKeys as $key)
                    {
                        $relatedTags[] = $group[$key]['id']; 
                    }
                }
                else
                {
                    $relatedTags[] = $group[$randKeys]['id']; 
                }
            }

            $offer->tags()->sync($relatedTags);
        }
    }
}
