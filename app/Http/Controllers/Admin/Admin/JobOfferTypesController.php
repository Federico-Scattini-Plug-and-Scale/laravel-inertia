<?php

namespace App\Http\Controllers\Admin\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJobOfferTypeRequest;
use App\Models\JobOfferType;
use Inertia\Inertia;

class JobOfferTypesController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/JobOfferTypes/Index', [
            'jobOfferTypes' => JobOfferType::all(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/JobOfferTypes/Create');
    }

    public function store(StoreJobOfferTypeRequest $request)
    {

        setStripeKey();

        $product = \Stripe\Product::create([
            'name' => 'T-shirt',
        ]);

        $price = \Stripe\Price::create([
            'product' => $product->id,
            'unit_amount' => 2000,
            'currency' => 'pln',
        ]);

        return redirect()->back();
    }
}
