<?php

use Illuminate\Support\Arr;

if (!function_exists('getCountry'))
{
	function getCountry()
	{
		return session()->get('country', config('countries.default.label'));
	}
}

if (!function_exists('getCountryName'))
{
	function getCountryName()
	{
		if (session()->has('country'))
		{
			$countryArr = array_filter(config('countries.availables'), fn ($item) => Arr::get($item, 'label') == session()->get('country'));
			$country = reset($countryArr);
			
			if (!empty($country))
			{
				return Arr::get($country, 'value');
			}
		}

		return config('countries.default.value');
	}
}