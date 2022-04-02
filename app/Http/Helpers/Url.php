<?php

if (!function_exists('queryParams'))
{
	function queryParams()
	{
		return str_replace(request()->url(), '', request()->fullUrl()) ;
	}
}