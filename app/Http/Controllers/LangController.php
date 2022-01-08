<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class LangController extends Controller
{
	public function __invoke()
    {
		$route = request()->get('route');
		$locale = request()->get('locale');
		
		if (Str::contains($route, 'admin'))
		{
			return redirect(route('admin.dashboard', [], true, $locale));
		}

		if (Str::contains($route, 'company'))
		{
			return redirect(route('company.dashboard', auth()->user(), true, $locale));
		}

		return redirect(route('home', [], true, $locale));
    }
}
