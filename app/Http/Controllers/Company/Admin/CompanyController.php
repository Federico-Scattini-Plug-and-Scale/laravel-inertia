<?php

namespace App\Http\Controllers\Company\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('currentUser', ['only' => ['edit', 'show']]);
    }

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

    public function edit(User $user, Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'email|required',
            'address' => 'string|required',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email
        ]);

        $detail = CompanyDetail::updateOrCreate([
            'user_id' => $user->id
        ],[
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'logo' => $request->logo,
            'website_link' => $request->website_link,
        ]);

        if (!$user->getHasCompanyDetails())
        {
            $detail->user()->save($user);
        }

        return redirect()->route('company.profile', $user);
    }
}
