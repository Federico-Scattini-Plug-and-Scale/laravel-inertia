<?php

use Stripe\Stripe;

if (!function_exists('setStripeKey')) {
  	function setStripeKey() {
		Stripe::setApiKey(env('STRIPE_PRIVATE_API_KEY'));
  	}
}