<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class LangController extends Controller
{
	public function __invoke()
    {
		return redirect(route(request()->get('route'), [], true, request()->get('locale')));
    }
}
