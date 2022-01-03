<?php

namespace App\Http\Controllers\Admin\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobOffer;
use App\Notifications\JobOffers\JobOfferPublished;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class JobOffersController extends Controller
{
    public function index()
    {
        $filters = request()->has('filters') ? request()->get('filters') : [];
        $jobOffers = JobOffer::getForAdmin(app()->getLocale(), 10, $filters);

        if (!empty($jobOffers))
        {
            $jobOffers->each(function($item) {
                if (!empty($item->published_at))
                {
                    $item->expiring_at = now('Europe/Rome')->subDays($item->validity_days)->endOfDay()->diffInDays(Carbon::parse($item->published_at, 'Europe/Rome'), false);
                }
                else
                {
                    $item->expiring_at = __('Expired');
                }
            });
        }

        return Inertia::render('Admin/JobOffers/Index', [
            'jobOffers' => $jobOffers,
            'statusOptions' => JobOffer::getStatusOptions(),
            'filters' => $filters
        ]);
    }

    public function approve(JobOffer $jobOffer)
    {
        $jobOffer->status = JobOffer::STATUS_ACTIVE;
        $jobOffer->published_at = now('Europe/Rome');
        $jobOffer->validity_days = JobOffer::VALIDITY;
        $jobOffer->save();

        foreach ($jobOffer->tags as $tag)
        {
            $tag->is_approved = true;
            $tag->is_active = true;
            $tag->save();
        }

        $jobOffer->company->notify(new JobOfferPublished());

        return redirect()->route('admin.joboffers.index')->with('success', __('The job offer was approved.'));
    }
}
