<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobOfferResource;
use App\Models\Category;
use App\Models\JobOffer;
use Inertia\Inertia;

class JobOffersController extends Controller
{
    public function index($category = 'all', $locations = null)
    {
        $offers = JobOfferResource::collection(JobOffer::getListing(100, getCountry(), $category, $locations));

        return Inertia::render('Front/JobOffers/Listing', [
            'offers' => $offers,
        ]);
    }

    public function show($categorySlug, $slug, JobOffer $jobOffer)
    {
        if ($slug != $jobOffer->slug || $categorySlug != $jobOffer->category->slug)
        {
            return redirect()->route('joboffers.show',[$jobOffer->category->slug, $jobOffer->slug, $jobOffer], 301);
        }

        return Inertia::render('Front/JobOffers/Offer', [
            'offer' => $jobOffer,
        ]);
    }
}
