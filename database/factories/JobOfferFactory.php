<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\JobOffer;
use App\Models\JobOfferType;
use App\Models\User;
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
        $jobOfferType = JobOfferType::all()->random();
        $categoriesId = array_map(fn ($item) => $item['id'], Category::select('id')->get()->toArray());
        
        return [
            'title' => $this->faker->text(40),
            'description' => $this->faker->text(200),
            'status' => $status,
            'specialization' => $this->faker->text(50),
            'company_id' => User::query()->where('role', 'company')->first()->id,
            'published_at' => $status == 'active' ? Carbon::today('Europe/Rome')->subDays(rand(0, 20)) : null,
            'rank' => 0,
            'job_offer_type_id' => $jobOfferType->id,
            'locale' => $jobOfferType->locale,
            'address' => $this->faker->address(),
            'latitude' => $this->faker->latitude(30, 60),
            'longitude' => $this->faker->longitude(8, 30),
            'validity_days' => 30,
            'category_id' => $this->faker->randomElement($categoriesId)
        ];
    }
}
