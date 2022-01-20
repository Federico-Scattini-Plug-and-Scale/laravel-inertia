<?php

namespace Database\Factories;

use App\Models\JobOfferType;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobOfferTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = JobOfferType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->text(20);
        setStripeKey();
        $productStripe = createStripeProduct($name);
        $price = $this->faker->randomElement([50.00, 75.00, 100.00]);
        $priceStripe = createStripePrice($productStripe->id, number_format(($price*100), 0, '', ''), 'EUR');

        return [
            'name' => $name,
            'stripe_product_name' => $name,
            'ranking' => 0,
            'currency' => 'EUR',
            'is_active' => true,
            'is_free' => false,
            'stripe_price_id' => $priceStripe->id,
            'stripe_product_id' => $productStripe->id,
            'price' => $price
        ];
    }
}
