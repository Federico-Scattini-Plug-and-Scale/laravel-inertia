<?php

namespace Database\Factories;

use App\Models\TagGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class TagGroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TagGroup::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->text(25),
            'is_active' => true,
            'position' => 0,
        ];
    }
}
