<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobOfferResource;
use App\Models\JobOffer;
use App\Models\Tag;
use App\Models\TagGroup;
use Inertia\Inertia;

class JobOffersController extends Controller
{
    public function index($category = 'all', $tech = 'all', $locations = null)
    {
        $offers = JobOfferResource::collection(JobOffer::getListing(100, getCountry(), $category, $locations));
        $markersInfo = JobOffer::getMarkers(getCountry(), $category, $locations);
        $technologies = Tag::getByGroupType(
                TagGroup::GROUP_TYPE_PROGRAMMING_LANGUAGE, 
                getCountry(), 
                ['id', 'name', 'slug', 'icon', 'bg_color']
            )
            ->each(function ($element) use ($category, $locations)
            {
                $element->icon_url = url('/img/' . $element->icon);
                $element->url = url(sprintf('/%s/%s/%s%s', $category, $element->slug, $locations ?? '', queryParams()));
            });

        return Inertia::render('Front/JobOffers/Listing', [
            'offers' => $offers,
            'markers' => $markersInfo,
            'technologies' => $technologies
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
