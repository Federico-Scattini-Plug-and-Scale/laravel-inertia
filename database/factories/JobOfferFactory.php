<?php

namespace Database\Factories;

use App\Models\JobOffer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class JobOfferFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = JobOffer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $status = $this->faker->randomElement(['active', 'inactive', 'unpaid']);

        return [
            'title' => $this->faker->title(),
            'description' => $this->faker->text(200),
            'status' => $status,
            'specialization' => $this->faker->text(50),
            'company_id' => $this->faker->randomElement([3, 5, 6, 7, 8, 9]),
            'published_at' => $status == 'active' ? Carbon::today('Europe/Rome')->subDays(rand(0, 50)) : null,
            'job_offer_type_id' => 36,
            'ranking' => 0,
            'locale' => 'it',
            'address' => 'Milano MI, Italia',
            'latitude' => 45.46420350,
            'longitude' => 9.18998200
        ];
    }
}
