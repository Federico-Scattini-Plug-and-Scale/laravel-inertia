<?php

namespace Database\Factories;

use App\Models\Tag;
use App\Models\TagGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class TagFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Tag::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $tagGroup = $this->faker->randomElement(TagGroup::all()->toArray());

        return [
            'name' => $this->faker->text(25),
            'is_active' => true,
            'is_approved' => true,
            'position' => 0,
            'tag_group_id' => $tagGroup['id'],
            'locale' => $tagGroup['locale']
        ];
    }
}
