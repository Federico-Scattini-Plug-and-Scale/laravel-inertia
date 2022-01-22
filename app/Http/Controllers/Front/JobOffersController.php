<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobOfferResource;
use App\Models\JobOffer;
use Inertia\Inertia;

class JobOffersController extends Controller
{
    public function index($locations = null)
    {
        $offers = JobOfferResource::collection(JobOffer::getListing(100, getCountry(), $locations));

        return Inertia::render('Front/JobOffers/Listing', [
            'offers' => $offers,
        ]);
    }
}
