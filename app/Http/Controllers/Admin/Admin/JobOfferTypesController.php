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
            'jobOfferTypes' => JobOfferType::getAllPaginated(10, getCountry()),
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

        return redirect()->back()->with('message', [
            'type' => 'success',
            'content' => __('The job offer type has been created successfully.')
        ]);
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

        return redirect()->back()->with('message', [
            'type' => 'success',
            'content' => __('The job offer type has been updated successfully.')
        ]);
    }

    public function destroy(JobOfferType $joboffertype)
    {
        $joboffertype->delete();

        return redirect()->back()->with('message', [
            'type' => 'success',
            'content' => __('The job offer type has been deleted successfully.')
        ]);
    }
}
