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

if (!function_exists('retrieveStripeProduct')) {
	function retrieveStripeProduct($id) {
		return Product::retrieve($id);
	}
}

if (!function_exists('updateStripeProduct')) {
	function updateStripeProduct($id, $params = []) {
		return Product::update($id, $params);
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

if (!function_exists('retrieveStripePrice')) {
	function retrieveStripePrice($id) {
		return Price::retrieve($id);
	}
}

if (!function_exists('updateStripePrice')) {
	function updateStripePrice($id, $params = []) {
		return Price::update($id, $params);
	}
}