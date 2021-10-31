<?php

namespace App\Http\Controllers\Company\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('currentUser', ['only' => ['edit', 'show']]);
    }

    public function index()
    {
        return Inertia::render('Company/Dashboard', [
            'company' => Auth::user()
        ]);
    }

    public function show(User $user)
    {
        return Inertia::render('Company/Profile', [
            'company' => $user,
        ]);
    }

    public function edit(User $user, Request $request)
    {
        $request->validate($this->validationRules($request->hasFile('logo')));

        $user->update([
            'name' => $request->name,
            'email' => $request->email
        ]);

        $oldLogo = '';
        if ($user->getHasCompanyDetails())
        {
            $oldLogo = $user->detail->logo;
        }

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $imageName = $logo->getClientOriginalName();
            $logo->move(public_path() . '/img/', $imageName);

            if (!empty($oldLogo))
                $this->deleteImage($oldLogo);
        }

        $detail = CompanyDetail::updateOrCreate([
            'user_id' => $user->id
        ],[
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'logo' => !empty($imageName) ? $imageName : (!empty($oldLogo) ? $oldLogo : null),
            'website_link' => $request->website_link,
        ]);

        if (!$user->getHasCompanyDetails())
        {
            $detail->user()->save($user);
        }

        return redirect()->route('company.profile', $user);
    }

    public function pricing()
    {
        return Inertia::render('Company/Pricing');
    }

    public function payment()
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_PRIVATE_API_KEY'));

        $product = \Stripe\Product::create([
            'name' => 'T-shirt',
        ]);

        $price = \Stripe\Price::create([
            'product' => $product->id,
            'unit_amount' => 2000,
            'currency' => 'pln',
        ]);

        $checkout_session = \Stripe\Checkout\Session::create([
            'customer_email' => Auth::user()->email,
            'line_items' => [[
                'price' => $price->id,
                'quantity' => 1,
            ]],
            'payment_method_types' => [
                'card',
            ],
            'mode' => 'payment',
            'success_url' => route('company.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('company.cancel'),
        ]);

        return redirect($checkout_session->url);
    }

    private function deleteImage($imageName)
    {
        if(File::exists(public_path('img/' . $imageName)))
        {
            File::delete(public_path('img/' . $imageName));
        }
    }

    private function validationRules($hasLogo)
    {
        $validationRules = [
            'name' => 'required',
            'email' => 'email|required',
            'address' => 'string|required',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ];

        if ($hasLogo)
            $validationRules['logo'] = 'image|mimes:jpg,png,jpeg,gif,svg|max:2048|dimensions:min_width=100,min_height=100,max_width=1000,max_height=1000';

        return $validationRules;
    }
}
