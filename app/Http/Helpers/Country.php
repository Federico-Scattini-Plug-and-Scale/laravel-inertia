<?php

use Illuminate\Support\Facades\Cookie;

if (!function_exists('getCountry'))
{
	function getCountry()
	{
		return Cookie::get('country', config('countries.default.label'));
	}
}