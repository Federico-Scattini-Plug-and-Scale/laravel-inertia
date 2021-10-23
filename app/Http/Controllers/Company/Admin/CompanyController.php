<?php

namespace App\Http\Controllers\Company\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompanyController extends Controller
{
    public function index()
    {
        return Inertia::render('Company/Dashboard');
    }

    public function show(User $user)
    {
        return Inertia::render('Company/Profile', [
            'company' => $user,
        ]);
    }
}
