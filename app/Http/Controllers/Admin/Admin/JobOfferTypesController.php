<?php

namespace App\Http\Controllers\Admin\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJobOfferTypeRequest;
use App\Models\JobOfferType;
use Illuminate\Support\Arr;
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
        $payload = $request->all();
        
        if (!Arr::get($payload, 'is_free'))
        {
            setStripeKey();

            $productStripe = createStripeProduct($request->stripe_product_name);
            $productStripeId = $productStripe->id;

            $priceStripe = createStripePrice($productStripeId, number_format((Arr::get($payload, 'price')*100), 0, '', ''), Arr::get($payload, 'currency'));
            $priceStripeId = $priceStripe->id;

            $payload['stripe_product_id'] = $productStripeId;
            $payload['stripe_price_id'] = $priceStripeId;
        }

        JobOfferType::create($payload);

        return redirect()->back();
    }
}
