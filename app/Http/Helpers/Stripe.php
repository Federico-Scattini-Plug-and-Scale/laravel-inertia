<?php

use Stripe\Price;
use Stripe\Product;
use Stripe\Stripe;

if (!function_exists('setStripeKey')) {
  	function setStripeKey() {
		Stripe::setApiKey(env('STRIPE_PRIVATE_API_KEY'));
  	}
}

if (!function_exists('createStripeProduct')) {
	function createStripeProduct($name) {
		return Product::create([
            'name' => $name,
        ]);
	}
}

if (!function_exists('createStripePrice')) {
	function createStripePrice($stripeProductId, $price, $currency) {
		return Price::create([
			'product' => $stripeProductId,
			'unit_amount' => $price,
			'currency' => $currency,
		]);
	}
}