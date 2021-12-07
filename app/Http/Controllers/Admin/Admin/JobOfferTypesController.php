<?php

namespace App\Http\Controllers\Admin\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\JobOfferTypeRequest;
use App\Models\JobOfferType;
use Illuminate\Support\Arr;
use Inertia\Inertia;

class JobOfferTypesController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/JobOfferTypes/Index', [
            'jobOfferTypes' => JobOfferType::getAllPaginated(10, app()->getLocale()),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/JobOfferTypes/Create');
    }

    public function store(JobOfferTypeRequest $request)
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

    public function edit(JobOfferType $joboffertype)
    {
        return Inertia::render('Admin/JobOfferTypes/Edit', [
            'jobOfferType' => $joboffertype
        ]);
    }

    public function update(JobOfferType $joboffertype, JobOfferTypeRequest $request)
    {
        $joboffertype->update($request->all());

        return redirect()->back();
    }

    public function destroy(JobOfferType $joboffertype)
    {
        $joboffertype->delete();

        return redirect()->back();
    }
}
